<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomeSectionsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('home_sections')) {
            Schema::create('home_sections', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('subtitle')->nullable();
                $table->string('type')->default('category'); // 'featured', 'new_arrivals', 'category', 'custom'
                $table->unsignedBigInteger('category_id')->nullable();
                $table->text('product_ids')->nullable();
                $table->integer('limit')->default(12);
                $table->tinyInteger('status')->default(1);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('home_sections');
    }
}
