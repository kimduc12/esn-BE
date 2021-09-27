<?php
namespace Database\Seeders;

use App\Constants\RolePermissionConst;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Topic;
use App\Constants\TopicConst;
use Illuminate\Support\Facades\DB;

class TopicsTableSeeder extends Seeder
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
        $topic = Topic::create([
            'name'         => $faker->name,
            'sort'         => 0,
            'status'       => TopicConst::STATUS_ACTIVE
        ]);
        $topic->categories()->sync([1]);
    }
}
