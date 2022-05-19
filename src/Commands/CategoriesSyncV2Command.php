<?php

namespace MarketDragon\Categorizable\Commands;

use Illuminate\Console\Command;
use Vanilo\Category\Models\Taxonomy;
use Vanilo\Category\Models\Taxon;

class CategoriesSyncV2Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketdragon:categories_sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import categories / taxonomies';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $categoryGroup = config('category.categories');

        foreach($categoryGroup as $key => $cluster) {
            $cluster = (object) $cluster;
            $taxons = json_decode(file_get_contents($cluster->source_path));
            $taxonomy = Taxonomy::firstOrCreate([
                'name' => $cluster->name,
            ]);
        }

        return 0;
    }

    public function createDescendants($taxonomy) {
        foreach($taxons as $key => $category) {
            $taxon = Taxon::updateOrCreate([
                'name' => $category->name,
                'taxonomy_id' => $taxonomy->id,
                'slug' => $category->slug,
                'priority' => $key,
            ]);

            foreach($category->children as $index => $ancestor) {
                $childTaxon = Taxon::firstOrCreate([
                    'name' => $ancestor->name,
                    'taxonomy_id' => $taxonomy->id,
                    'slug' => $ancestor->slug,
                    'parent_id' => $taxon->id,
                    'priority' => $index,
                ]);

                foreach($ancestor->children as $i => $descendant)
                {
                    Taxon::firstOrCreate([
                        'name' => $descendant->name,
                        'slug' => $descendant->slug,
                        'taxonomy_id' => $taxonomy->id,
                        'parent_id' => $childTaxon->id,
                        'priority' => $i,
                    ]);
                }
            }
        }
    }
}
