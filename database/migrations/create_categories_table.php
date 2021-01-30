<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('social.categories.table'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->defualt(0);
            $table->string('title')->unique();
            $table->timestamps();

            $table->foreign('parent_id')
            ->references('id')
            ->on(config('social.categories.table'))
            ->onDelete('cascade');
        });

        Schema::create('categoriables', function (Blueprint $table) {
            $table->integer('tag_id')->unsigned();
            $table->morphs('taggable');

            $table->foreign('tag_id')
                ->references('id')
                ->on(config('social.categories.table'))
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('social.categories.categories_table'));
        Schema::dropIfExists('taggables');
    }
}
