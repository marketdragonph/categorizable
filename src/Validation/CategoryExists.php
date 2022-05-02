<?php

namespace MarketDragon\Categorizable\Validation;

use MarketDragon\Categorizable\Categorization;
use Illuminate\Contracts\Validation\Rule;
use MarketDragon\Categorizable\CategoryRepository;

class CategoryExists implements Rule
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
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value, $parameters = [])
    {
        return (bool) $this->findCategory($value);
    }

     /**
      * Get the validation error message.
      *
      * @return string
      */
    public function message()
    {
        return 'The :attribute you are using is invalid';
    }
}
