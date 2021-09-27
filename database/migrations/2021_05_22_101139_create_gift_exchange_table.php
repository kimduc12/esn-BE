<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftExchangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gift_exchange', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('gift_id');
            $table->string('gift_code');
            $table->string('gift_name');
            $table->integer('gift_points')->default(0);
            $table->string('gift_image_url')->nullable();
            $table->string('gift_image_mobile_url')->nullable();
            $table->timestamp('return_at')->nullable();
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
        Schema::dropIfExists('gift_exchange');
    }
}
