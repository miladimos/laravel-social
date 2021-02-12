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
        Schema::create(config('social.likes.table'), function (Blueprint $table) {
            $table->id();
            $table->foreignId(config('social.likes.liker_foreign_key'))->index()->comment('user_id');
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
        Schema::dropIfExists(config('social.likes.table'));
    }
}
