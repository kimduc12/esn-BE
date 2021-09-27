<?php
namespace Database\Seeders;

use App\Constants\RolePermissionConst;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Age;
use App\Constants\AgeConst;
use Illuminate\Support\Facades\DB;

class AgesTableSeeder extends Seeder
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
        $age = Age::create([
            'name'         => $faker->name,
            'sort'         => 0,
            'status'       => AgeConst::STATUS_ACTIVE
        ]);
        $age->categories()->sync([1]);
    }
}
