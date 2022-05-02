<?php

namespace MarketDragon\Categorizable\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use MarketDragon\Categorizable\CategoryRepository;

class Synchronize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'categories:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize categories from configured source json file.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach (config('category.clusters') as $name => $config) {
            $repository = new CategoryRepository($name);

            $rootNode = $repository->createRootOrFirst();

            $this->info("Root category [{$rootNode->name}] created.");

            if (file_exists($sourcePath = Arr::get($config, 'source_path'))) {
                $descendants = json_decode(file_get_contents($sourcePath));

                $this->traverse($descendants, $rootNode, $rootNode->name);
            }
        }
    }

    /**
      * @param array $categories
      * @param \MarketDragon\Categorizable\Category $parent
      *
      * @return void
      */
    private function traverse(array $categories, $parent, string $cluster)
    {
        $repository = new CategoryRepository($cluster);

        foreach ($categories as $category) {
            $childNode = $repository->findBySlug(Str::slug($category->name));

            if (is_null($childNode)) {
                $childNode = $repository->create(['name' => $category->name], $parent);
            }

            $this->info("Child category [{$childNode->name}] of [{$parent->name}] created.");

            if (isset($category->children)) {
                $this->traverse($category->children, $childNode, $cluster);
            }
        }
    }
}
