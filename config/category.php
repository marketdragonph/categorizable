<?php

return [
    'model' => MarketDragon\Categorizable\Category::class,

    'clusters' => [
        'products' => [
            'model' => \App\Models\Product::class,
            'source_path' => env('CATEGORIES_JSON_PATH', __DIR__ . '/../resources/products.json'),
        ],

        'stores' => [
            'model' => \App\Models\Store::class
        ],

        'blog_posts' => [
            'model' => \App\Models\BlogPost::class
        ]
    ],
];
