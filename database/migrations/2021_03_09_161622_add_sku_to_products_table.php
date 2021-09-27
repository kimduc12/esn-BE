<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSkuToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->after('price_original');
            }
            if (!Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode')->nullable()->after('price_original');
            }
            if (!Schema::hasColumn('products', 'stock_address')) {
                $table->string('stock_address')->nullable()->after('price_original');
            }
            if (!Schema::hasColumn('products', 'quantity')) {
                $table->integer('quantity')->default(0)->after('price_original');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sku');
            $table->dropColumn('barcode');
            $table->dropColumn('stock_address');
            $table->dropColumn('quantity');
        });
    }
}
