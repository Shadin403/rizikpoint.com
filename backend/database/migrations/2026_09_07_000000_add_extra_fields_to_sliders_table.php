<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFieldsToSlidersTable extends Migration
{
    public function up()
    {
        Schema::table('sliders', function (Blueprint $table) {
            if (!Schema::hasColumn('sliders', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('sliders', 'badge')) {
                $table->string('badge')->nullable()->after('button_text');
            }
            if (!Schema::hasColumn('sliders', 'type')) {
                $table->string('type')->default('main')->after('badge');
            }
        });
    }

    public function down()
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropColumn(['description', 'badge', 'type']);
        });
    }
}
