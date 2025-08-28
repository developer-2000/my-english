<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('en_words', function (Blueprint $table) {
            $table->id();
            $table->string('word')->nullable()->default(null);
            $table->string('translation')->nullable()->default(null);
            $table->string('url_image')->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->tinyInteger('type_id')->default(0)->comment('type word');
            $table->text('time_forms')->nullable()->default(null);
            $table->tinyInteger('is_known')->nullable()->default(null)
                ->comment('null - не отвечал, 0 - не знаю, 1 - знаю');
            $table->integer('unknown_order')->nullable()
                ->comment('порядок попадания в "не знаю"');
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
        Schema::dropIfExists('en_words');
    }
}
