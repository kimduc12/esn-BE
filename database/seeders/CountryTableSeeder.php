<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Constants\CountryConst;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('countries')->truncate();
        $faker  = \Faker\Factory::create();
        for($i=1; $i<=4; $i++){
            $country = Country::create([
                'name'         => $faker->country,
                'sort'         => 0,
                'status'       => CountryConst::STATUS_ACTIVE
            ]);
        }

    }
}
