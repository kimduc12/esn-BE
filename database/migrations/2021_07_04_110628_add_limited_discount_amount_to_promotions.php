<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLimitedDiscountAmountToPromotions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('promotions', 'limited_discount_amount'))
        {
            Schema::table('promotions', function (Blueprint $table) {
                $table->integer('limited_discount_amount')->default(0)->after('discount_amount');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('promotions', 'limited_discount_amount'))
        {
            Schema::table('promotions', function (Blueprint $table) {
                $table->dropColumn('limited_discount_amount');
            });
        }
    }
}
