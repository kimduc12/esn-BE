<?php
namespace Database\Seeders;

use App\Constants\RolePermissionConst;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Constants\UserConst;
use Illuminate\Support\Facades\DB;

class ClientTableSeeder extends Seeder
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
            $user = User::create([
                'name' => $faker->name,
                'username' => $faker->userName,
                'email' => $faker->email(),
                'password' => Hash::make('123456'),
                'phone' => $faker->e164PhoneNumber,
                'gender' => $faker->boolean(50),
                'birthday' => $faker->date,
                'address' => $faker->address,
                'status' => UserConst::STATUS_ACTIVE,
            ]);
            $user->assignRole(RolePermissionConst::ROLE_CUSTOMER);
        }
    }
}
