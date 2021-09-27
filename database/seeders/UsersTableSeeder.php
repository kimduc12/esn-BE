<?php
namespace Database\Seeders;

use App\Constants\RolePermissionConst;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Constants\UserConst;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();
        $admin = User::create([
            'name' => 'super admin',
            'username' =>'superadmin',
            'email' => 'superadmin@yopmail.com',
            'password' => Hash::make('123456'),
            'phone' => Str::random(10),
            'status' => UserConst::STATUS_ACTIVE,
        ]);
        $admin->assignRole(RolePermissionConst::ROLE_SUPER_ADMIN);
        $user = User::create([
            'name' => 'admin',
            'username' =>'admin',
            'email' => 'admin@yopmail.com',
            'password' => Hash::make('123456'),
            'phone' => Str::random(10),
            'status' => UserConst::STATUS_ACTIVE,
        ]);
        $user->assignRole(RolePermissionConst::ROLE_ADMIN);
    }
}
