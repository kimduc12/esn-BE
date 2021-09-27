<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageUrlToOrderDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('product_option_id');
            $table->string('image_mobile_url')->nullable()->after('image_url');
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
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('image_url');
            $table->dropColumn('image_mobile_url');
            $table->dropColumn('discount_total_price');
            $table->dropColumn('promotion_id');
        });
    }
}
