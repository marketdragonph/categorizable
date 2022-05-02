### Dependencies
1. https://github.com/lazychaser/laravel-nestedset

### Setup
Publishes config and migration file.

```
$ php artisan vendor:publish --provider="MarketDragon\Categorizable\CategorizableServiceProvider"
$ php artisan migrate
```

#### Model Setup
Use `CategorizableTrait` in particular cluster model.

```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MarketDragon\Categorizable\CategorizableTrait;

class Pussy extends Model
{
    use CategorizableTrait;
}
```

Then register the cluster model class into the `config/category.php` file.

```
'clusters' => [
    'pussies' => [
        'model' => App\Models\Pussy::class,
    ],
]
```

Finally, synchronize configured cluster category models. This will seed root category and data when `source_path` json file is given.

```
$ php artisan categories:sync
```

### Usage
To get the repository instance of particular categories using the model static method `categories`.

```
Pussy::categories(); // MarketDragon\Categorizable\CategoryRepository
```

Or instantiate manually.

```
use MarketDragon\Categorizable\CategoryRepository;

$repository = new CategoryRepository('cluster_name'); // `cluster_name` is the key name set in the config file.

// Or using the model class

$repository = new CategoryRepository(Pussy::class);
```

### Repository Methods

```
/**
 * Transform the categories collection into "heirarchy" structure.
 *
 * @return \Kalnoy\Nestedset\Collection
 */
public function getTree();
```
```
/**
 * Get all siblings category at the given depth level.
 *
 * @param integer $depth
 * @return \Kalnoy\Nestedset\Collection
 */
public function allInDepth(int $depth = 1)
```
```
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
```
```
/**
 * Find clustered child category by ID.
 *
 * @param integer $id
 * @return \MarketDragon\Categorizable\Category
 */
public function findById(int $id)
```
```
/**
 *  Find clustered child category by name.
 *
 * @param string $name
 * @return \MarketDragon\Categorizable\Category
 */
public function findByName(string $name)
```
```
/**
 *  Find clustered child category by slug.
 *
 * @param string $slug
 * @return \MarketDragon\Categorizable\Category
 */
public function findBySlug(string $slug)
```

### CategorizableTrait Methods
```
/**
 * Sets the category of model.
 *
 * @param  \MarketDragon\Categorizable\Category|int
 * @return int
 */
public function setCategory($category)
```
```
/**
 * Unsets category of model.
 *
 * @return int
 */
public function unsetCategory()
```
```
/**
 * Instantiates new category repository instance of model.
 *
 * @return \MarketDragon\Categorizable\CategoryRepository
 */
public static function categories()
```

### Scope Query
```
/**
 * @param \Illuminate\Database\Eloquent\Builder $query
 * @param \MarketDragon\Categorizable\Category|int|string $category
 *
 * @return \Illuminate\Database\Eloquent\Builder
 */
public function scopeWhereCategory(Builder $query, $category)
```

### Model Methods
```
/**
 * Get the root category of current node.
 *
 * @return self
 */
public function getRootNode()
```
```
/**
 * Determine the category last descendant of given node.
 *
 * @param self $node
 * @return boolean
 */
public function isLastDescendantOf(self $node)
```
```
/**
 * Determine the category has no children means last descendant.
 *
 * @return boolean
 */
public function isLastDescendant()
```
