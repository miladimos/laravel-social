<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('social.follows.table'), function (Blueprint $table) {
            $table->id();
            $table->morphs('followable'); // follower
            $table->morphs('followingable'); // following
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
        });

        Schema::create(config('social.likes.table'), function (Blueprint $table) {
            $table->id();
            $table->foreignId(config('social.likes.liker_foreign_key'))->index()->comment('user_id');
            $table->morphs(config('social.likes.morphs'));
            $table->timestamps();

            $table->unique(['likeable_id', 'likeable_type'], 'likes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('social.follows.table'));
        Schema::dropIfExists(config('social.likes.table'));
    }
};
