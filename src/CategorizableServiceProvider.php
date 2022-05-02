<?php

namespace MarketDragon\Categorizable;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use MarketDragon\Categorizable\Categorization;
use MarketDragon\Categorizable\Commands\Synchronize;

class CategorizableServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();
        $this->publishMigrations();
        
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
               Synchronize::class
            ]);
        }

        //$this->registerDynamicRelations();
    }

    protected function registerDynamicRelations()
    {
        $model = Categorization::getModel();
        $clusters = Categorization::getClusters();

        foreach ($clusters as $name => $config) {
            $model::resolveRelationUsing(Str::camel($name),
                function ($instance) use ($config) {
                    return $instance->morphedByMany(
                        $config['model'],
                        'categorizable'
                    );
                }
            );
        }
    }

    protected function mergeConfig()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/category.php', 'category');
    }

    protected function publishConfig()
    {
        $this->publishes([
            __DIR__.'/../config/category.php' => config_path('category.php')
        ], 'config');
    }

    protected function publishMigrations()
    {
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');
    }
}
