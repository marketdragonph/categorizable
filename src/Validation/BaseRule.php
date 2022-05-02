<?php

namespace MarketDragon\Categorizable\Validation;

use Illuminate\Contracts\Validation\Rule;
use MarketDragon\Categorizable\CategoryRepository;

abstract class BaseRule implements Rule
{
    /**
     * @var string
     */
    protected $cluster;

     /**
      * @var string
      */
    protected $column;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $cluster, string $column = 'slug')
    {
        $this->cluster = $cluster;
        $this->column = $column;
    }

    /**
     * Get repository instance.
     *
     * @return \MarketDragon\Categorizable\CategoryRepository
     */
    protected function repository()
    {
        return new CategoryRepository($this->cluster);
    }

    /**
     * Find category according by given key.
     *
     * @param mixed $value
     * @return void
     */
    protected function findCategory($value)
    {
        switch ($this->column) {
            case 'id':
                return $this->repository()->findById($value);
            case 'slug':
                return $this->repository()->findBySlug($value);
            case 'name':
                return $this->repository()->findByName($value);
        }
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    abstract public function passes($attribute, $value);

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    abstract public function message();
}
