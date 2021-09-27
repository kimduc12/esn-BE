<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreInfoToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('sub_phone')->nullable();
            $table->integer('city_id')->default(0);
            $table->integer('district_id')->default(0);
            $table->integer('ward_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'sub_phone')) {
                $table->dropColumn('sub_phone');
            }
            $table->dropColumn('city_id');
            $table->dropColumn('district_id');
            $table->dropColumn('ward_id');
        });
    }
}
