<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookmarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('social.subscription.subscriptions_table'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(config('favorite.user_foreign_key'))->index()->comment('user_id');
            $table->morphs('favoriteable');
            $table->unique(['likeable_id', 'likeable_type'], 'subscription');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('social.subscription.subscriptions_table'));
    }
}
