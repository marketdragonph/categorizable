<?php

return [
    'model' => MarketDragon\Categorizable\Category::class,

    'clusters' => [
        'products' => [
            'model' => \App\Models\Product::class,
            'source_path' => env('CATEGORIES_JSON_PATH',  '/home/sail/packages/categorizable/resources/categories.json'),
        ],

        'stores' => [
            'model' => \App\Models\Store::class
        ],

        'posts' => [
            'model' => \App\Models\Post::class
        ]
    ],
];
