<?php
namespace Database\Seeders;

use App\Constants\RolePermissionConst;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\ProductCategory;
use App\Constants\ProductCategoryConst;
use Illuminate\Support\Facades\DB;

class ProductCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //DB::table('ages')->truncate();
        $faker  = \Faker\Factory::create();
        ProductCategory::create([
            'name'             => $faker->name,
            'slug'             => $faker->slug,
            'image_url'        => $faker->imageUrl(),
            'image_mobile_url' => $faker->imageUrl(),
            'title'            => $faker->name,
            'keyword'          => $faker->name,
            'description'      => $faker->name,
            'parent_id'        => 0,
            'sort'             => 0,
            'status'           => ProductCategoryConst::STATUS_ACTIVE
        ]);
    }
}
