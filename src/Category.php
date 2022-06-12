<?php

namespace MarketDragon\Categorizable;

use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

class Category extends Model
{
    use NodeTrait, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        '_lft',
        '_rgt',
        'parent_id',
        'slug',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    public static function booted()
    {
        static::saving(function ($category) {
            $category->slug = Str::slug($category->name, '-');
        });
    }

    /**
     * Get the root category of current node.
     *
     * @return self
     */
    public function getRootNode()
    {
        return $this->ancestors()->whereIsRoot()->first();
    }

    /**
     * Determine the category last descendant of given node.
     *
     * @param self $node
     * @return boolean
     */
    public function isLastDescendantOf(self $node)
    {
        $target = $node
            ->children()
            ->reversed()
            ->first();
        
        return ($node->isAncestorOf($this) && ($target->id == $this->id));
    }

    /**
     * Determine the category has no children means last descendant.
     *
     * @return boolean
     */
    public function isLastDescendant()
    {
        return $this->children()->count() === 0;
    }
}
