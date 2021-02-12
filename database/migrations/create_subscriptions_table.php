<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('social.subscription.table'), function (Blueprint $table) {
            $table->id();
            $table->foreignId(config('social.subscription.subscriber_foreign_key'))->index()->comment('user_id');
            $table->morphs(config('social.subscription.morphs'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('social.subscription.table'));
    }
}
