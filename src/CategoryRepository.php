<?php

namespace MarketDragon\Categorizable;

use MarketDragon\Categorizable\Exceptions\CategorizableException;

class CategoryRepository
{
    /**
     * @var string
     */
    protected $cluster;

    /**
     * Create a new repository instance.
     *
     * @param  string $cluster
     * @return void
     */
    public function __construct(string $cluster = null)
    {
        $this->cluster = $this->validateCluster($cluster);
    }

    /**
     * Get clustered categories collection.
     *
     * @return \Kalnoy\Nestedset\Collection
     */
    public function getCollection()
    {
        return $this->getRoot()->children()->get();
    }

    /**
     * Transform the categories collection into "heirarchy" structure.
     *
     * @return \Kalnoy\Nestedset\Collection
     */
    public function getTree()
    {
        return $this->getCollection()->toTree();
    }

    /**
     * Get all siblings category at the given depth level.
     *
     * @param integer $depth
     * @return \Kalnoy\Nestedset\Collection
     */
    public function allInDepth(int $depth = 1)
    {
        return $this->clusteredQuery()
            ->withDepth()
            ->having('depth', '>=', max(1, $depth))
            ->get();
    }

    /**
     * Create the root node of the current cluster if not exists.
     *
     * @return \MarketDragon\Categorizable\Category
     *
     * @throws \MarketDragon\Categorizable\Exceptions\CategorizableException
     */
    public function createRootOrFirst()
    {
        $rootNode = $this->getRoot();

        if (is_null($rootNode)) {
            $rootNode = (Categorization::getModel())::create([
                'name' => $this->cluster
            ]);
        }

        return $rootNode;
    }

    /**
     * Create a child category of the given parent.
     *
     * @param array $attributes
     * @param  int|null $parent
     * @return \MarketDragon\Categorizable\Category
     *
     * @throws \MarketDragon\Categorizable\Exceptions\CategorizableException
     */
    public function create(array $attributes, $parent = null)
    {
        if (is_null($parent)) {
            $parent = $this->getRoot();
        }

        if (is_numeric($parent)) {
            $parent = $this->findById($parent);
        }

        if (! $parent->isRoot() && ! $parent->isDescendantOf($this->getRoot())) {
            throw CategorizableException::unrelatedParent($parent, $this->getRoot());
        }

        return (Categorization::getModel())::create($attributes, $parent);
    }

    /**
     * Find clustered child category by ID.
     *
     * @param integer $id
     * @return \MarketDragon\Categorizable\Category
     */
    public function findById(int $id)
    {
        return $this->clusteredQuery()->whereKey($id)->first();
    }

    /**
     *  Find clustered child category by name.
     *
     * @param string $name
     * @return \MarketDragon\Categorizable\Category
     */
    public function findByName(string $name)
    {
        return $this->clusteredQuery()->whereName($name)->first();
    }

    /**
     *  Find clustered child category by slug.
     *
     * @param string $slug
     * @return \MarketDragon\Categorizable\Category
     */
    public function findBySlug(string $slug)
    {
        return $this->clusteredQuery()->whereSlug($slug)->first();
    }

    /**
     * Determine particular cluster already has root node.
     *
     * @return boolean
     */
    public function hasRoot()
    {
        return ! is_null($this->getRoot());
    }

    /**
     * Get the clustered category root node.
     *
     * @return \MarketDragon\Categorizable\Category
     */
    public function getRoot()
    {
        return static::baseQuery()
            ->whereIsRoot()
            ->whereName($this->cluster)
            ->first();
    }

    /**
     * Get and add clustered constraints query into the builder.
     *
     * @return \Kalnoy\Nestedset\QueryBuilder
     */
    public function clusteredQuery()
    {
        return static::baseQuery()->whereDescendantOf($this->getRoot());
    }

    /**
     * Validate model/cluster name.
     *
     * @param string $cluster
     * @return string
     *
     * @throws \MarketDragon\Categorizable\Exceptions\CategorizableException
     */
    protected function validateCluster(string $cluster)
    {
        if (! Categorization::hasModelOrCluster($cluster)) {
            throw CategorizableException::unknownCluster($cluster);
        }

        return Categorization::getClusterByModel($cluster) ?? $cluster;
    }

    /**
     * Get the query builder without clustered category constraints.
     *
     * @return \Kalnoy\Nestedset\QueryBuilder
     */
    public static function baseQuery()
    {
        return (Categorization::getModel())::query();
    }
}
