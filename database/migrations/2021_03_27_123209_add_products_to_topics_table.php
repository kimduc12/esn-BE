<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductsToTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->text('summary')->nullable()->after('name');
            $table->string('image_uhd_url')->nullable()->after('summary');
            $table->string('image_fhd_url')->nullable()->after('image_uhd_url');
            $table->string('image_hd_url')->nullable()->after('image_fhd_url');
            $table->string('image_mb_url')->nullable()->after('image_hd_url');
            $table->boolean('is_active')->default(0)->after('sort');
            $table->string('title')->nullable()->after('updated_at');
            $table->text('keyword')->nullable()->after('title');
            $table->text('description')->nullable()->after('keyword');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropColumn('summary');
            $table->dropColumn('is_active');
            $table->dropColumn('title');
            $table->dropColumn('keyword');
            $table->dropColumn('description');
        });
    }
}
