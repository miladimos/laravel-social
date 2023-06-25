<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->morphs('followerable');
            $table->morphs('followingable');
            $table->boolean('requested')->default(false);
            $table->timestamp('requested_at')->nullable();
            $table->boolean('accepted')->default(false);
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
        });

        Schema::create(config('social.tags.table'), function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->index();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create(config('social.tags.taggables'), function (Blueprint $table) {
            $table->foreignId('tag_id');
            $table->morphs(config('social.tags.morphs'));

            $table->foreign('tag_id')
                ->references('id')
                ->on(config('social.tags.table'))
                ->onDelete('cascade');
        });

        //
        //        Schema::create(config('social.bookmarks.table'), function (Blueprint $table) {
        //            $table->id();
        //            $table->unsignedBigInteger(config('social.bookmarks.bookmarker_foreign_key'))->index()->comment('user_id');
        //            $table->morphs(config('social.bookmarks.morphs'));
        //        });
        //
        //        Schema::create(config('social.comments.table'), function (Blueprint $table) {
        //            $table->id();
        //            $table->foreignId('parent_id')->default(0);
        //            $table->morphs('commentorable');
        //            $table->morphs('commentable');
        //            $table->text('comment');
        //            $table->string('guest_name')->nullable();
        //            $table->string('guest_email')->nullable();
        //            $table->boolean('approved')->default(false);
        // $table->timestamp('approved_at')->nullable();
        //            $table->timestamps();
        //
        //            $table->index(["commentable_type", "commentable_id"]);
        //
        //            $table->foreign('parent_id')
        //                ->references('id')
        //                ->on(config('social.comments.table'))->onDelete('cascade')->onUpdate('cascade');
        //        });
        //
        //        Schema::create(config('social.likes.table'), function (Blueprint $table) {
        //            $table->id();
        //            $table->foreignId(config('social.likes.liker_foreign_key'))->index()->comment('user_id');
        //            $table->morphs(config('social.likes.morphs'));
        //            $table->timestamps();
        //
        //            $table->unique(['likeable_id', 'likeable_type'], 'likes');
        //        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('social.follows.table'));
        //
        //        Schema::dropIfExists(config('social.bookmarks.table'));
        //        Schema::dropIfExists(config('social.categories.table'));
        //        Schema::dropIfExists(config('social.categories.pivot_table'));
        //        Schema::dropIfExists(config('social.comments.table'));
        //        Schema::dropIfExists(config('social.likes.table'));
        //        Schema::dropIfExists(config('social.tags.table'));
        //        Schema::dropIfExists(config('social.tags.pivot_table'));
    }
};
