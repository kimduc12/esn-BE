<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionToBlogCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('blog_categories', 'description'))
        {
            Schema::table('blog_categories', function (Blueprint $table) {
                $table->text('description')->nullable();
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
        if (Schema::hasColumn('blog_categories', 'description'))
        {
            Schema::table('blog_categories', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
}
