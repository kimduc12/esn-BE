<?php
namespace Database\Seeders;

use App\Constants\RolePermissionConst;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::table('model_has_permissions')->truncate();
        //DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        // create permissions
        $permissions = RolePermissionConst::getPermissionByRole(RolePermissionConst::ROLE_SUPER_ADMIN);
        foreach($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'api']);
        }
        // create role and assign permission to it
        $roles = array_merge(RolePermissionConst::ROLES, [RolePermissionConst::ROLE_SUPER_ADMIN]);
        foreach($roles as $role) {
            $role = Role::create(['name' => $role, 'guard_name' => 'api']);
            $permissions = RolePermissionConst::getPermissionByRole($role->name);
            $role->givePermissionTo($permissions);
        }

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
