<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('social.follows.table'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_id')->index();
            $table->foreignId('following_id')->index();
            $table->boolean('requested')->default(false);
            $table->boolean('accepted')->default(false);
            $table->timestamp('accepted_at')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('social.follows.table'));
    }
}
