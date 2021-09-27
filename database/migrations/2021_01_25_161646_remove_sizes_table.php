<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('sizes');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('sort')->default(0);
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }
}
