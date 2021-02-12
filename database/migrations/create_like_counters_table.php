<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikeCountersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('social.follows.counter_table'), function (Blueprint $table) {
            $table->id();
            $table->string('likeable_id', 36);
            $table->string('likeable_type', 255);
            $table->unsignedBigInteger('count')->default(0);
            $table->unique(['likeable_id', 'likeable_type'], 'likes_counts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('social.follows.counter_table'));
    }
}
