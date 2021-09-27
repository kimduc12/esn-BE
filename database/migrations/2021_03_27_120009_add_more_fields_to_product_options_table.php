<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreFieldsToProductOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_options', function (Blueprint $table) {
            $table->string('barcode')->nullable()->after('sku');
            $table->string('stock_address')->nullable()->after('quantity');
            $table->integer('price_original')->default(0)->after('price');
            $table->string('image_url')->nullable()->after('stock_address');
            $table->string('image_mobile_url')->nullable()->after('image_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_options', function (Blueprint $table) {
            $table->dropColumn('barcode');
            $table->dropColumn('stock_address');
            $table->dropColumn('price_original');
            $table->dropColumn('image_url');
            $table->dropColumn('image_mobile_url');
        });
    }
}
