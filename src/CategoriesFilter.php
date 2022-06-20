<?php

namespace MarketDragon\Categorizable;

use EloquentFilter\ModelFilter;

class CategoriesFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = ['products'];

    public function name($name) {
        if ($name) return $this->where('name', 'LIKE', '%' . $name . '%');

        return $this;
    }

    public function ids($ids) {
        return $this->whereIn('id', $ids);
    }
}
