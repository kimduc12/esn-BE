<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('is_verified')->default(0)->after('status');
            $table->integer('channel')->default(0)->after('is_verified');
            $table->integer('paid_status')->default(0)->after('total_price');
            $table->integer('delivery_status')->default(0)->after('cancel_at');
            $table->integer('cod_status')->default(0)->after('delivered_at');
            $table->integer('cod_price')->default(0)->after('cod_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('is_verified');
            $table->dropColumn('channel');
            $table->dropColumn('paid_status');
            $table->dropColumn('delivery_status');
            $table->dropColumn('cod_status');
            $table->dropColumn('cod_price');
        });
    }
}
