<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_infos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id');
            $table->integer('badge')->default(0);
            $table->bigInteger('lastest_order_id')->nullable();
            $table->timestamp('lastest_order_at')->nullable();
            $table->integer('average_total_money_per_order')->default(0);
            $table->integer('total_spent_money')->default(0);
            $table->integer('total_orders')->default(0);
            $table->integer('total_points')->default(0);
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('customer_infos');
    }
}
