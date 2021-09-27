<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePromotionToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->bigInteger('promotion_program_id')->default(0)->after('receiver_ward_id');
            $table->renameColumn('promotion_id', 'promotion_code_id');
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
            $table->dropColumn('promotion_program_id');
            $table->renameColumn('promotion_code_id', 'promotion_id');
        });
    }
}
