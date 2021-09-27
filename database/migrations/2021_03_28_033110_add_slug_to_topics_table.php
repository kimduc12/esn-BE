<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlugToTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $indexs = collect(DB::select("SHOW INDEXES FROM topics"))->pluck('Key_name');

        Schema::table('topics', function (Blueprint $table) use ($indexs) {
            if ($indexs->contains('topics_name_unique')) {
                $table->dropUnique('topics_name_unique');
            }
            if (!Schema::hasColumn('topics', 'slug')) {
                $table->string('slug')->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $indexs = collect(DB::select("SHOW INDEXES FROM topics"))->pluck('Key_name');
        Schema::table('topics', function (Blueprint $table) use ($indexs) {
            if ($indexs->contains('topics_slug_unique')) {
                $table->dropUnique('topics_slug_unique');
            }
            if (Schema::hasColumn('topics', 'slug')) {
                $table->dropColumn('slug');
            }
            $table->string('name')->unique()->change();
        });
    }
}
