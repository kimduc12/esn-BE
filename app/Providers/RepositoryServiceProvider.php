<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->bind('App\Repositories\UserInterface', 'App\Repositories\Eloquent\UserRepository');
        $this->app->bind('App\Repositories\RoleInterface', 'App\Repositories\Eloquent\RoleRepository');
        $this->app->bind('App\Repositories\SettingInterface', 'App\Repositories\Eloquent\SettingRepository');
        $this->app->bind('App\Repositories\BannerInterface', 'App\Repositories\Eloquent\BannerRepository');
        $this->app->bind('App\Repositories\BlogInterface', 'App\Repositories\Eloquent\BlogRepository');
        $this->app->bind('App\Repositories\BlogCategoryInterface', 'App\Repositories\Eloquent\BlogCategoryRepository');
        $this->app->bind('App\Repositories\ProductInterface', 'App\Repositories\Eloquent\ProductRepository');
        $this->app->bind('App\Repositories\ProductCategoryInterface', 'App\Repositories\Eloquent\ProductCategoryRepository');
        $this->app->bind('App\Repositories\AgeInterface', 'App\Repositories\Eloquent\AgeRepository');
        $this->app->bind('App\Repositories\TopicInterface', 'App\Repositories\Eloquent\TopicRepository');
        $this->app->bind('App\Repositories\ProductTypeInterface', 'App\Repositories\Eloquent\ProductTypeRepository');
        $this->app->bind('App\Repositories\SupplierInterface', 'App\Repositories\Eloquent\SupplierRepository');
        $this->app->bind('App\Repositories\PageInterface', 'App\Repositories\Eloquent\PageRepository');
        $this->app->bind('App\Repositories\AttributeInterface', 'App\Repositories\Eloquent\AttributeRepository');
        $this->app->bind('App\Repositories\OrderInterface', 'App\Repositories\Eloquent\OrderRepository');
        $this->app->bind('App\Repositories\ShippingInterface', 'App\Repositories\Eloquent\ShippingRepository');
        $this->app->bind('App\Repositories\CityInterface', 'App\Repositories\Eloquent\CityRepository');
        $this->app->bind('App\Repositories\DistrictInterface', 'App\Repositories\Eloquent\DistrictRepository');
        $this->app->bind('App\Repositories\WardInterface', 'App\Repositories\Eloquent\WardRepository');
        $this->app->bind('App\Repositories\BrandInterface', 'App\Repositories\Eloquent\BrandRepository');
        $this->app->bind('App\Repositories\PatternInterface', 'App\Repositories\Eloquent\PatternRepository');
        $this->app->bind('App\Repositories\MaterialInterface', 'App\Repositories\Eloquent\MaterialRepository');
        $this->app->bind('App\Repositories\SectionInterface', 'App\Repositories\Eloquent\SectionRepository');
        $this->app->bind('App\Repositories\CountryInterface', 'App\Repositories\Eloquent\CountryRepository');
        $this->app->bind('App\Repositories\FileInterface', 'App\Repositories\Eloquent\FileRepository');
        $this->app->bind('App\Repositories\AdvicePostInterface', 'App\Repositories\Eloquent\AdvicePostRepository');
        $this->app->bind('App\Repositories\GiftInterface', 'App\Repositories\Eloquent\GiftRepository');
        $this->app->bind('App\Repositories\GiftExchangeInterface', 'App\Repositories\Eloquent\GiftExchangeRepository');
        $this->app->bind('App\Repositories\CartInterface', 'App\Repositories\Eloquent\CartRepository');
        $this->app->bind('App\Repositories\RouteInterface', 'App\Repositories\Eloquent\RouteRepository');
        $this->app->bind('App\Repositories\PromotionInterface', 'App\Repositories\Eloquent\PromotionRepository');
    }
}
