<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreFieldsToGifts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gifts', function (Blueprint $table) {
            $table->bigInteger('supplier_id')->default(0)->after('id');
            $table->integer('price')->default(0)->after('points');
            $table->timestamp('start_datetime')->nullable()->after('price');
            $table->timestamp('end_datetime')->nullable()->after('start_datetime');
            $table->longText('content')->nullable()->after('end_datetime');
            $table->integer('sort')->default(0)->after('end_date');
            $table->timestamp('published_at')->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gifts', function (Blueprint $table) {
            $table->dropColumn('supplier_id');
            $table->dropColumn('price');
            $table->dropColumn('start_datetime');
            $table->dropColumn('end_datetime');
            $table->dropColumn('content');
            $table->dropColumn('sort');
            $table->dropColumn('published_at');
        });
    }
}
