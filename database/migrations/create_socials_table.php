<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSocialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('social.bookmarks.table'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(config('social.bookmarks.bookmarker_foreign_key'))->index()->comment('user_id');
            $table->morphs(config('social.bookmarks.morphs'));
        });

        Schema::create(config('social.categories.table'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->defualt(0);
            $table->string('title')->unique();
            $table->string('slug')->unique()->nullable();
            $table->boolean('active')->default(config('social.categories.default_active'));
            $table->timestamps();

            $table->foreign('parent_id')
                ->references('id')
                ->on(config('social.categories.table'))
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::create(config('social.categories.pivot_table'), function (Blueprint $table) {
            $table->unsignedBigInteger('category_id');
            $table->morphs(config('social.categories.morphs'));

            $table->foreign('category_id')
                ->references('id')
                ->on(config('social.categories.table'))
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::create(config('social.comments.table'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->default(0);
            $table->morphs('commentorable');
            $table->morphs('commentable');
            $table->text('comment');
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->boolean('approved')->default(false);
            $table->timestamps();

            $table->index(["commentable_type", "commentable_id"]);

            $table->foreign('parent_id')
                ->references('id')
                ->on(config('social.comments.table'))->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create(config('social.follows.table'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_id')->index();
            $table->foreignId('following_id')->index();
            $table->boolean('requested')->default(false);
            $table->boolean('accepted')->default(false);
            $table->timestamp('accepted_at')->nullable()->index();
        });

        Schema::create(config('social.likes.table'), function (Blueprint $table) {
            $table->id();
            $table->foreignId(config('social.likes.liker_foreign_key'))->index()->comment('user_id');
            $table->morphs(config('social.likes.morphs'));
            $table->timestamps();

            $table->unique(['likeable_id', 'likeable_type'], 'likes');
        });

        Schema::create(config('social.follows.counter_table'), function (Blueprint $table) {
            $table->id();
            $table->string('likeable_id', 36);
            $table->string('likeable_type', 255);
            $table->unsignedBigInteger('count')->default(0);
            $table->unique(['likeable_id', 'likeable_type'], 'likes_counts');
        });

        Schema::create(config('social.subscription.table'), function (Blueprint $table) {
            $table->id();
            $table->foreignId(config('social.subscription.subscriber_foreign_key'))->index()->comment('user_id');
            $table->morphs(config('social.subscription.morphs'));
            $table->timestamps();
        });

        Schema::create(config('social.tags.table'), function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique()->nullable();
            $table->boolean('active')->default(config('social.tags.default_active'));
            $table->timestamps();
        });

        Schema::create(config('social.tags.pivot_table'), function (Blueprint $table) {
            $table->unsignedBigInteger('tag_id');
            $table->morphs(config('social.tags.morphs'));

            $table->foreign('tag_id')
                ->references('id')
                ->on(config('social.tags.table'))
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('social.bookmarks.table'));
        Schema::dropIfExists(config('social.categories.table'));
        Schema::dropIfExists(config('social.categories.pivot_table'));
        Schema::dropIfExists(config('social.comments.table'));
        Schema::dropIfExists(config('social.follows.table'));
        Schema::dropIfExists(config('social.likes.table'));
        Schema::dropIfExists(config('social.follows.counter_table'));
        Schema::dropIfExists(config('social.subscription.table'));
        Schema::dropIfExists(config('social.tags.table'));
        Schema::dropIfExists(config('social.tags.pivot_table'));
    }
}
