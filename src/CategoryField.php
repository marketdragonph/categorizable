<?php

namespace MarketDragon\Categorizable;

trait CategoryField {
  public $categorySearch;
  public $categories = [];

  public $categories_rules = [
    'categories' => 'required|array|min:2',
  ];

  public function getCategoryTrailProperty()
  {
    if (empty($this->categories)) return [];

    return Category::withDepth()
        ->filter([ 'ids' => $this->categories ])
        ->get();

  }

  public function initializeCategories($model)
  {
    $categories = $model
            ->category
            ->pluck('id')->toArray();
    $this->fill(['categories' => $categories ]);
  }

  public function getCurrentNodeProperty() {
        
    if (!empty($this->categories)) {
            return Category::withDepth()
                ->firstWhere('id', end($this->categories));
    }
        
    return null;
  }

  public function getCategoryOptionsProperty()
  {
      return Category::withDepth()
          ->when(empty($this->categories), function ($query) {
              return $query->having('depth', '=', 1);
          })
          ->when($this->categories, function ($query) {
              return $query
                  ->when(!$this->currentNode->isLeaf(), function ($query) {
                      return $query
                          ->whereDescendantOf(end($this->categories));
                  })
                  ->when($this->currentNode->isLeaf(), function ($query) {
                      $query->whereDescendantOf($this->currentNode->parent);
                  });
          })
          ->filter([ 'name' => $this->categorySearch ])
          ->defaultOrder()
          ->get();
  }

  public function resetCategories() {
      $this->reset(['categories']);
  }
}