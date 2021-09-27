<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image_url')->nullable();
            $table->string('image_mobile_url')->nullable();
            $table->string('top_image_url')->nullable();
            $table->string('top_image_mobile_url')->nullable();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('title')->nullable();
            $table->text('keyword')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('is_hot')->default(0);
            $table->tinyInteger('is_top')->default(0);
            $table->tinyInteger('is_sub_top')->default(0);
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
        Schema::dropIfExists('blogs');
    }
}
