<?php

namespace MarketDragon\Categorizable;

use Ankurk91\Eloquent\MorphToOne;
use Illuminate\Database\Eloquent\Builder;
use MarketDragon\Categorizable\CategoryRepository;
use MarketDragon\Categorizable\Exceptions\CategorizationException;

trait CategorizableTrait
{
    use MorphToOne;

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \MarketDragon\Categorizable\Category|int|string $category
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereCategory(Builder $query, $category)
    {
        if (is_numeric($category)) {
            $category = static::categories()->findById($category);
        }

        if (is_string($category)) {
            $category = static::categories()->findBySlug($category);
        }

        return $query->whereHas('category', function ($query) use ($category) {
            $query->whereKey($category->id);
        });
    }

    /**
     * Sets the category of model.
     *
     * @param  \MarketDragon\Categorizable\Category|int
     * @return int
     */
    public function setCategory($category)
    {
        if (is_numeric($category)) {
            $category = static::categories()->findById($category);
        }

        if (is_string($category)) {
            $category = static::categories()->findBySlug($category);
        }

        if (! $category->isLastDescendantOf(static::categories()->getRoot())) {
            throw CategorizationException::unassignable($category, $this);
        }

        return $this->category()->sync($category);
    }

    /**
     * Unsets category of model.
     *
     * @return int
     */
    public function unsetCategory()
    {
        return $this->category()->detach();
    }

    /**
     * Get the category relation of model.
     *
     * @return \Ankurk91\Eloquent\Relations\MorphToOne
     */
    public function category()
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }

    /**
     * Instantiates new category repository instance of model.
     *
     * @return \MarketDragon\Categorizable\CategoryRepository
     */
    public static function categories()
    {
        return new CategoryRepository(static::class);
    }
}
