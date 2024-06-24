<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoSentenceSoundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ro_sentence_sounds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sentence_id')->index();
            $table->foreign('sentence_id')
                ->references('id')
                ->on('ro_sentences')
                ->onUpdate('cascade')
                ->onDelete('cascade');
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
        Schema::dropIfExists('ro_sentence_sounds');
    }
}
