<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
        Schema::dropIfExists(config('social.tags.table'));
        Schema::dropIfExists(config('social.tags.pivot_table'));
    }
}
