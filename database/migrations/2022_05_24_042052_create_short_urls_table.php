<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShortUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('short_urls', function (Blueprint $table) {
            $table->id();
            $table->string('original_url',2083);
            $table->string('short_url')->unique();
            $table->string('scheme');
            $table->string('host');
            $table->string('port');
            $table->string('user');
            $table->string('pass');
            $table->string('path',2083);
            $table->string('query',2083);
            $table->string('fragment',2083);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('short_urls');
    }
}
