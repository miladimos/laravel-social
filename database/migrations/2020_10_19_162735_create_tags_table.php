<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('social.likes.likes_table'), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->morphs(config('social.likes.morphs'));
            $table->timestamps();

            $table->unique(['likeable_id', 'likeable_type'], 'likes');
        });

        Schema::create('taggables', function (Blueprint $table) {
            $table->integer('tag_id')->unsigned();
            $table->morphs('taggable');

            $table->foreign('tag_id')
                ->references('id')
                ->on('tags')
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
        Schema::dropIfExists(config('social.likes.likes_table'));
        Schema::dropIfExists('taggables');
    }
}
