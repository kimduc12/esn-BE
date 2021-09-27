<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreFieldsToCartDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_details', function (Blueprint $table) {
            $table->bigInteger('cart_id')->after('id');
            $table->integer('price')->default(0)->after('product_option_id');
            $table->integer('total_price')->default(0)->after('price');
            $table->integer('discount_total_price')->default(0)->after('total_price');
            $table->bigInteger('promotion_id')->default(0)->after('discount_total_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart_details', function (Blueprint $table) {
            $table->dropColumn('cart_id');
            $table->dropColumn('price');
            $table->dropColumn('total_price');
            $table->dropColumn('discount_total_price');
            $table->dropColumn('promotion_id');
        });
    }
}
