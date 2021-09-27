<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Constants\ProductConst;
use App\Models\Age;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Country;
use App\Models\Material;
use App\Models\Pattern;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductType;
use App\Models\Supplier;
use App\Models\Topic;
use App\Models\Route;
use App\Constants\RouteConst;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Product::truncate();
        //DB::table('product_category')->truncate();
        //DB::table('product_options')->truncate();
        //DB::table('productables')->truncate();
        try {
            DB::beginTransaction();
            for($i=1; $i<=6; $i++) {
                $faker  = \Faker\Factory::create();
                $supplier = Supplier::whereStatus(1)->get()->random(1)->first();
                $brand = Brand::whereStatus(1)->get()->random(1)->first();
                $country = Country::whereStatus(1)->get()->random(1)->first();
                $name = $faker->name;
                $has_options = 0;
                if ($i % 2 == 0) {
                    $has_options = 1;
                }
                $data = [
                    'name'             => $name,
                    'slug'             => Str::slug($name),
                    'image_url'        => $faker->imageUrl,
                    'image_mobile_url' => $faker->imageUrl,
                    'summary'          => $faker->sentence,
                    'content'          => $faker->paragraph,
                    'quote'            => 'Lorem ipsum',
                    'price'            => $faker->numberBetween(10000, 100000),
                    'price_original'   => $faker->numberBetween(1000, 9900),
                    'quantity'         => $faker->numberBetween(1, 100),
                    'stock_address'    => $faker->address,
                    'barcode'          => $faker->ean13,
                    'sku'              => $faker->randomDigit,
                    'star_rating'      => $faker->numberBetween(1, 5),
                    'supplier_id'      => $supplier->id ?? 0,
                    'brand_id'         => $brand->id ?? 0,
                    'country_id'       => $country->id ?? 0,
                    'title'            => $name,
                    'keyword'          => $name,
                    'description'      => $name,
                    'can_promotion'    => $faker->boolean(50),
                    'is_popular'       => $faker->boolean(50),
                    'is_show'          => 1,
                    'status'           => ProductConst::STATUS_ACTIVE,
                    'total_orders'     => 0,
                    'has_options'       => $has_options,
                    'published_at'     => Carbon::yesterday(),
                ];

                if ($has_options == 1) {
                    $data['price'] = 0;
                    $data['price_original'] = 0;
                    $data['quantity'] = 0;
                    $data['stock_address'] = null;
                    $data['barcode'] = null;
                    $data['sku'] = null;
                }

                $product = Product::create($data);
                $categories = ProductCategory::whereParentId(0)->whereStatus(1)->get();
                if($categories->isNotEmpty()) {
                    $category_ids = $categories->pluck('id')->toArray();
                    $product->categories()->sync($category_ids);
                }


                $ages = Age::whereStatus(1)->get();
                if($ages->isNotEmpty()) {
                    $age_ids = $ages->pluck('id')->toArray();
                    $product->ages()->sync($age_ids);
                }

                $topics = Topic::whereStatus(1)->get();
                if($topics->isNotEmpty()) {
                    $topic_ids = $topics->pluck('id')->toArray();
                    $product->topics()->sync($topic_ids);
                }

                $product_types = ProductType::whereStatus(1)->get();
                if($product_types->isNotEmpty()) {
                    $product_type_ids = $product_types->pluck('id')->toArray();
                    $product->product_types()->sync($product_type_ids);
                }

                $patterns = Pattern::whereStatus(1)->get();
                if($patterns->isNotEmpty()) {
                    $pattern_ids = $patterns->pluck('id')->toArray();
                    $product->patterns()->sync($pattern_ids);
                }

                $materials = Material::whereStatus(1)->get();
                if($materials->isNotEmpty()) {
                    $material_ids = $materials->pluck('id')->toArray();
                    $product->materials()->sync($material_ids);
                }

                if ($has_options == 1) {
                    for ($j = 0; $j <= 2 ; $j++) {
                        $options = [
                            'sku'              => $faker->randomDigit,
                            'barcode'          => $faker->ean13,
                            'price'            => $faker->numberBetween(10000, 100000),
                            'price_original'   => $faker->numberBetween(1000, 9900),
                            'quantity'         => $faker->numberBetween(1, 100),
                            'stock_address'    => $faker->address,
                            'image_url'        => $faker->imageUrl,
                            'image_mobile_url' => $faker->imageUrl,
                        ];
                        $color_array = ['Đỏ','Xanh','Vàng','Đen'];
                        $size_array = ['S','X','M','L'];
                        $attributes = Attribute::get();
                        foreach($attributes as $attribute) {
                            $slug = $attribute->slug;
                            switch($slug) {
                                case 'size':
                                    $options[$slug] = $size_array[$j];
                                    break;
                                case 'color':
                                    $options[$slug] = $color_array[$j];
                                    break;
                            }
                        }
                        $product->product_options()->createMany([$options]);
                    }
                }
                Route::create([
                    'slug' => $product->slug,
                    'type' => RouteConst::TYPE_PRODUCT,
                    'type_id' => $product->id,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $this->command->error($e->getMessage());
        }
    }
}
