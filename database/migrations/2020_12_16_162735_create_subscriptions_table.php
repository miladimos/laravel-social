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
        Schema::create(config('social.subscription.subscriptions_table'), function (Blueprint $table) {
            $table->id();
            $table->foreignId(config('social.subscription.user_id'))->index()->comment('user_id');
            $table->morphs(config('social.subscription.morphs'));
            $table->timestamps();

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
