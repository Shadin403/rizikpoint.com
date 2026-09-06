<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddButtonTextToSlidersTable extends Migration
{
    public function up()
    {
        Schema::table('sliders', function (Blueprint $table) {
            if (!Schema::hasColumn('sliders', 'button_text')) {
                $table->string('button_text')->nullable()->after('link');
            }
        });
    }

    public function down()
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropColumn('button_text');
        });
    }
}
