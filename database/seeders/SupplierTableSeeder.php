<?php
namespace Database\Seeders;

use App\Constants\RolePermissionConst;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Supplier;
use App\Constants\SupplierConst;
use Illuminate\Support\Facades\DB;

class SupplierTableSeeder extends Seeder
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
        Supplier::create([
            'name'         => $faker->name,
            'sort'         => 0,
            'status'       => SupplierConst::STATUS_ACTIVE
        ]);
    }
}
