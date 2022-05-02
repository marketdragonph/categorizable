<?php

namespace MarketDragon\Categorizable\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;

class CategorizableException extends Exception
{
    public static function unknownCluster(string $cluster)
    {
        return new static('Unknown cluster named '. $cluster . '.');
    }

    public static function unrelatedParent(object $parent, object $root)
    {
        return new static('Category '. $parent->id .' is not belong to ' . $root->name . ' categories.');
    }

    public static function unassignable(object $category, Model $model)
    {
        return new static('Category '. $category->id . ' cannot be assigned to ' . get_class($model) . '::' . $model->id);
    }
}
