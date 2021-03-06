<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{

    public function up()
    {
        Schema::create(config('social.comments.table'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->default(0);
            // $table->foreignId('commentor_id')->nullable();
            $table->morphs('commentorable');
            $table->morphs('commentable');
            $table->text('comment');
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->boolean('approved')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->index(["commentable_type", "commentable_id"]);

            $table->foreign('parent_id')
                ->references('id')
                ->on(config('social.comments.table'))->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('social.comments.table'));
    }
}
