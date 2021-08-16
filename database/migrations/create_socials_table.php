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
        Schema::create(config('social.table'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(config('social.bookmarker_foreign_key'))->index()->comment('user_id');
            $table->morphs(config('social.morphs'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('social.table'));
    }
}
