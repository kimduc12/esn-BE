<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPromotionToCarts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->bigInteger('promotion_program_id')->default(0)->after('gift_message');
            $table->bigInteger('promotion_code_id')->default(0)->after('promotion_program_id');
            $table->string('promotion_code')->nullable()->after('promotion_code_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('promotion_program_id');
            $table->dropColumn('promotion_code_id');
            $table->dropColumn('promotion_code');
        });
    }
}
