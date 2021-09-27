<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Constants\RouteConst;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Topic;
use App\Repositories\RouteInterface;
use App\Repositories\ProductCategoryInterface;
use App\Repositories\ProductInterface;
use App\Repositories\BlogCategoryInterface;
use App\Repositories\BlogInterface;
use App\Repositories\TopicInterface;

class SyncRoutesFromDatabase extends Command
{
    protected $route;
    protected $productCategory;
    protected $product;
    protected $blogCategory;
    protected $blog;
    protected $topic;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-route';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all routes from database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        RouteInterface $route,
        ProductCategoryInterface $productCategory,
        ProductInterface $product,
        BlogCategoryInterface $blogCategory,
        BlogInterface $blog,
        TopicInterface $topic
    )
    {
        parent::__construct();
        $this->route = $route;
        $this->productCategory = $productCategory;
        $this->product = $product;
        $this->blogCategory = $blogCategory;
        $this->blog = $blog;
        $this->topic = $topic;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::table('routes')->truncate();
        $productCategories = ProductCategory::all();
        foreach ($productCategories as $productCategory) {
            if(!$productCategory->slug || $productCategory->slug == '' || $productCategory->slug == null) {
                continue;
            }
            $this->route->create([
                'slug' => $productCategory->slug,
                'type' => RouteConst::TYPE_PRODUCT_CATEGORY,
                'type_id' => $productCategory->id
            ]);
        }

        $products = Product::all();
        foreach ($products as $product) {
            if(!$product->slug || $product->slug == '' || $product->slug == null) {
                continue;
            }
            $this->route->create([
                'slug' => $product->slug,
                'type' => RouteConst::TYPE_PRODUCT,
                'type_id' => $product->id
            ]);
        }

        $blogCategories = BlogCategory::all();
        foreach ($blogCategories as $blogCategory) {
            if(!$blogCategory->slug || $blogCategory->slug == '' || $blogCategory->slug == null) {
                continue;
            }
            $this->route->create([
                'slug' => $blogCategory->slug,
                'type' => RouteConst::TYPE_BLOG_CATEGORY,
                'type_id' => $blogCategory->id
            ]);
        }

        $blogs = Blog::all();
        foreach ($blogs as $blog) {
            if(!$blog->slug || $blog->slug == '' || $blog->slug == null) {
                continue;
            }
            $this->route->create([
                'slug' => $blog->slug,
                'type' => RouteConst::TYPE_BLOG,
                'type_id' => $blog->id
            ]);
        }

        $topics = Topic::all();
        foreach ($topics as $topic) {
            if(!$topic->slug || $topic->slug == '' || $topic->slug == null) {
                continue;
            }
            $this->route->create([
                'slug' => $topic->slug,
                'type' => RouteConst::TYPE_TOPIC,
                'type_id' => $topic->id
            ]);
        }
        return 0;
    }
}
