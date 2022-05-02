<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategorizationsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('categories');

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->nestedSet();
            $table->timestamps();
        });

        Schema::create('categorizations', function (Blueprint $table) {
            $table->morphs('categorizable');
            $table->foreignId('category_id')->constrained('categories');

            $table->unique([
                'categorizable_id',
                'categorizable_type',
                'category_id'
            ], 'categorizations_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categorizations');
        Schema::dropIfExists('categories');
    }
}
