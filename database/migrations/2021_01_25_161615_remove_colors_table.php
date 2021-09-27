<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveColorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('colors');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('colors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('sort')->default(0);
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }
}
