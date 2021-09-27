<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUnusedFieldsToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivery_status');
            $table->dropColumn('delivering_at');
            $table->dropColumn('delivered_at');
            $table->dropColumn('cod_status');
            $table->dropColumn('cod_price');
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
            $table->integer('delivery_status')->default(0)->after('cancel_at');
            $table->timestamp('delivering_at')->nullable()->after('delivery_status');
            $table->timestamp('delivered_at')->nullable()->after('delivering_at');
            $table->integer('cod_status')->default(0)->after('delivered_at');
            $table->integer('cod_price')->default(0)->after('cod_status');
        });
    }
}
