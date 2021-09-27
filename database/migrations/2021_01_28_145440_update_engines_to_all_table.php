<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateEnginesToAllTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = [
            'users',
            'products',
            'ages',
            'banners',
            'blogs',
            'blog_categories',
            'blog_category',
            'category_age',
            'category_topic',
            'failed_jobs',
            'migrations',
            'model_has_permissions',
            'model_has_roles',
            'oauth_access_tokens',
            'oauth_auth_codes',
            'oauth_clients',
            'oauth_personal_access_clients',
            'oauth_refresh_tokens',
            'password_resets',
            'permissions',
            'product_categories',
            'product_category',
            'roles',
            'role_has_permissions',
            'settings',
            'topics',

        ];
        foreach ($tables as $table) {
            DB::statement('ALTER TABLE ' . $table . ' ENGINE = InnoDB');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {}
}
