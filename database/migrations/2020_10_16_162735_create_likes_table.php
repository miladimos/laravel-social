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
            $table->foreignId(config('social.likes.user_id'))->index();
            $table->morphs(config('social.likes.morphs'));
            $table->timestamps();

            $table->unique(['likeable_id', 'likeable_type'], 'likes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('likes');
    }
}
