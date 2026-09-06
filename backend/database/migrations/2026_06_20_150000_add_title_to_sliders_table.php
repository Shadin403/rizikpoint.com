<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleToSlidersTable extends Migration
{
    public function up()
    {
        Schema::table('sliders', function (Blueprint $table) {
            if (!Schema::hasColumn('sliders', 'title')) {
                $table->string('title')->nullable()->after('id');
            }
        });
    }

    public function down()
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }
}
