<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('code')->unique()->nullable();
            $table->integer('is_common_use')->default(0);
            $table->integer('group_type')->default(0);
            $table->integer('type')->default(0);
            $table->integer('discount_amount')->default(0);
            $table->integer('total_used_amount')->default(0);
            $table->integer('limited_amount')->default(0);
            $table->boolean('is_never_limited')->default(0);
            $table->timestamp('start_datetime')->nullable();
            $table->timestamp('end_datetime')->nullable();
            $table->boolean('is_never_expired')->default(0);
            $table->integer('apply_type_1')->default(0);
            $table->integer('apply_value_1')->default(0);
            $table->integer('apply_type_2')->default(0);
            $table->integer('apply_value_2')->default(0);
            $table->bigInteger('created_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotions');
    }
}
