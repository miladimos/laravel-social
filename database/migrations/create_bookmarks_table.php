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
        Schema::create(config('social.bookmarks.table'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(config('social.bookmarks.bookmarker_foreign_key'))->index()->comment('user_id');
            $table->morphs(config('social.bookmarks.morphs'));
            $table->unique(['bookmarkable_id', 'bookmarkable_type'], 'subscription');
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
    }
}
