<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCodeToGifts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('gifts', 'code'))
        {
            Schema::table('gifts', function (Blueprint $table) {
                $table->dropColumn('code');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('gifts', 'code'))
        {
            Schema::table('gifts', function (Blueprint $table) {
                $table->string('code')->unique()->after('supplier_id');
            });
        }
    }
}
