<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToCartDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_details', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('product_option_id');
            $table->string('image_mobile_url')->nullable()->after('image_url');
            $table->string('sku')->nullable()->after('image_mobile_url');
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
            $table->dropColumn('image_url');
            $table->dropColumn('image_mobile_url');
            $table->dropColumn('sku');
        });
    }
}
