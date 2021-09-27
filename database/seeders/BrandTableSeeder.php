<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Constants\BrandConst;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BrandTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('brands')->truncate();
        $faker  = \Faker\Factory::create();
        for($i=1; $i<=4; $i++){
            $brand = Brand::create([
                'name'         => $faker->name,
                'sort'         => 0,
                'status'       => BrandConst::STATUS_ACTIVE
            ]);
        }

    }
}
