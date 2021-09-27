<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gift;
use App\Constants\GiftConst;
use Carbon\Carbon;

class GiftTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker  = \Faker\Factory::create();
        for ($i=1; $i<=30; $i++) {
            Gift::create([
                'code'             => $faker->bankAccountNumber,
                'name'             => $faker->name,
                'image_url'        => $faker->imageUrl(),
                'image_mobile_url' => $faker->imageUrl(),
                'min_user_badge'   => $faker->numberBetween(1,5),
                'quantity'         => $faker->numberBetween(10,100),
                'points'           => $faker->numberBetween(1000,5000),
                'start_date'       => Carbon::now(),
                'end_date'         => Carbon::now()->addMonths(6),
                'status'           => GiftConst::STATUS_ACTIVE,
            ]);
        }
    }
}
