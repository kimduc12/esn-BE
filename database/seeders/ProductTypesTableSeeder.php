<?php
namespace Database\Seeders;

use App\Constants\RolePermissionConst;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\ProductType;
use App\Constants\ProductTypeConst;
use Illuminate\Support\Facades\DB;

class ProductTypesTableSeeder extends Seeder
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
        $type = ProductType::create([
            'name'         => $faker->name,
            'sort'         => 0,
            'status'       => ProductTypeConst::STATUS_ACTIVE
        ]);
        $type->categories()->sync([1]);
    }
}
