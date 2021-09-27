<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Rinvex\Attributes\Models\Attribute;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Attribute::typeMap([
            'varchar' => \Rinvex\Attributes\Models\Type\Varchar::class,
            'boolean' => \Rinvex\Attributes\Models\Type\Boolean::class,
            'integer' => \Rinvex\Attributes\Models\Type\Integer::class
        ]);
        Schema::defaultStringLength(191);
        $this->app->register(RepositoryServiceProvider::class);

    }
}
