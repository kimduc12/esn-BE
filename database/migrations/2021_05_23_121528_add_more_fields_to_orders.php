<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreFieldsToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('gift_message')->nullable()->after('is_gift_wrapping');
            $table->string('receiver_name')->nullable()->after('gift_message');
            $table->string('receiver_phone')->nullable()->after('receiver_name');
            $table->text('receiver_address')->nullable()->after('receiver_phone');
            $table->bigInteger('receiver_city_id')->default(0)->after('receiver_address');
            $table->bigInteger('receiver_district_id')->default(0)->after('receiver_city_id');
            $table->bigInteger('receiver_ward_id')->default(0)->after('receiver_district_id');
            $table->bigInteger('promotion_id')->default(0)->after('receiver_ward_id');
            $table->string('promotion_code')->nullable()->after('promotion_id');
            $table->integer('payment_type')->default(0)->after('promotion_code');
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
            $table->dropColumn('gift_message');
            $table->dropColumn('receiver_name');
            $table->dropColumn('receiver_phone');
            $table->dropColumn('receiver_address');
            $table->dropColumn('receiver_city_id');
            $table->dropColumn('receiver_district_id');
            $table->dropColumn('receiver_ward_id');
            $table->dropColumn('promotion_id');
            $table->dropColumn('promotion_code');
            $table->dropColumn('payment_type');
        });
    }
}
