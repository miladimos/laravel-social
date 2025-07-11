<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_follows', function (Blueprint $table) {
            $table->id();
            $table->morphs('followable'); // follower
            $table->morphs('followingable'); // following
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('social_likes', function (Blueprint $table) {
            $table->id();
            $table->morphs('likerable'); // user
            $table->morphs('likeable'); // model
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_follows');
        Schema::dropIfExists('social_likes');
    }
};
