<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shippings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id');
            $table->string('shipping_code');
            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->string('from_address');
            $table->string('lat_from_address');
            $table->string('lng_from_address');
            $table->string('to_address');
            $table->string('lat_to_address');
            $table->string('lng_to_address');
            $table->timestamp('expect_pickup_at')->nullable();
            $table->timestamp('pickup_at')->nullable();
            $table->timestamp('expect_delivered_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->integer('delivery_status')->default(0);
            $table->integer('cod_price')->default(0);
            $table->integer('cod_status')->default(0);
            $table->integer('shipping_fee')->default(0);
            $table->text('notes')->nullable(0);
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('shippings');
    }
}
