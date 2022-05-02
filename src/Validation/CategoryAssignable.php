<?php

namespace MarketDragon\Categorizable\Validation;

class CategoryAssignable extends BaseRule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value, $parameters = [])
    {
        return $this->findCategory($value)->isLastDescendant();
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
