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
            $table->string('slug')->unique();
            $table->boolean('active')->default(config('social.categories.default_active'));
            $table->timestamps();

            $table->foreign('parent_id')
            ->references('id')
            ->on(config('social.categories.table'))
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });

        Schema::create('categoriables', function (Blueprint $table) {
            $table->integer('category_id')->unsigned();
            $table->morphs(config('social.categories.morphs'));

            $table->foreign('category_id')
                ->references('id')
                ->on(config('social.categories.table'))
                >onUpdate('cascade')
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
        Schema::dropIfExists(config('social.categories.table'));
        Schema::dropIfExists(config('social.categories.morphs'));
    }
}
