<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image_url')->nullable();
            $table->string('image_mobile_url')->nullable();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->integer('price')->default(0);
            $table->integer('price_discount')->default(0);
            $table->integer('discount_percent')->default(0);
            $table->integer('star_rating')->default(0);
            $table->string('title')->nullable();
            $table->text('keyword')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('is_home_discount')->default(0);
            $table->tinyInteger('is_home_farvorite')->default(0);
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
        Schema::dropIfExists('products');
    }
}
