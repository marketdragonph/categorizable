<?php

namespace MarketDragon\Categorizable;

trait CategoryField {
  public $categorySearch;
  public $selected_categories = [];

  public function getCategoryTrailProperty()
  {
      if (empty($this->selected_categories)) return [];

      return Category::withDepth()
          ->filter([ 'ids' => $this->selected_categories ])
          ->get();

  }

  public function getCurrentNodeProperty() {
        
    if (!empty($this->selected_categories)) {
            return Category::withDepth()
                ->firstWhere('id', end($this->selected_categories));
    }
        
    return null;
  }

  public function getCategoriesProperty()
  {


      return Category::withDepth()
          ->when(empty($this->selected_categories), function ($query) {
              return $query->having('depth', '=', 1);
          })
          ->when($this->selected_categories, function ($query) {
              return $query
                  ->when(!$this->currentNode->isLeaf(), function ($query) {
                      return $query
                          ->whereDescendantOf(end($this->selected_categories));
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
      $this->reset(['selected_categories']);
  }
}