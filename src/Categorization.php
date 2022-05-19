<?php

namespace MarketDragon\Categorizable;

class Categorization
{
    /**
     * Get the class model of categorizable.
     *
     * @return string
     */
    public static function getModel()
    {
        return config('category.model');
    }

    /**
     * Get all configured clusters.
     *
     * @return array
     */
    public static function getClusters()
    {
        return config('category.clusters');
    }

    /**
     * Determine the given cluster/model is configured.
     *
     * @param string $cluster
     * @return boolean
     */
    public static function hasModelOrCluster(string $cluster)
    {
        return static::hasModel($cluster) || static::hasCluster($cluster);
    }

    /**
     * Determine given cluster name is configured.
     *
     * @param string $name
     * @return boolean
     */
    public static function hasCluster(string $name)
    {
        return ! is_null(static::getModelByCluster($name));
    }

    /**
     * Determine the given model is configured.
     *
     * @param \Illuminate\Database\Eloquent\Model|string $model
     * @return boolean
     */
    public static function hasModel($model)
    {
        if (is_object($model)) {
            $model = get_class($model);
        }

        return ! is_null(static::getClusterByModel($model));
    }

    /**
     * Get the cluster name by the given model
     *
     * @param \Illuminate\Database\Eloquent\Model|string $model
     * @return string
     */
    public static function getClusterByModel($model)
    {
        $clusters = config('category.clusters');

        if (is_object($model)) {
            $model = get_class($model);
        }

        foreach ($clusters as $name => $config) {
            if (is_a($config['model'], $model, true)) {
                return $name;
            }
        }

        return null;
    }

    /**
     * Get the model class of the given cluster.
     *
     * @param string $name
     * @return string
     */
    public static function getModelByCluster(string $name)
    {
        $clusters = config('category.clusters');

        if (array_key_exists($name, $clusters)) {
            return $clusters[$name]['model'];
        }

        return $name;
    }
}
