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
            $table->string('short_url');
            $table->string('scheme');
            $table->string('host');
            $table->string('port');
            $table->string('user');
            $table->string('pass');
            $table->string('path',2083);
            $table->string('query',2083);
            $table->string('fragment',2083);
            $table->unsignedBigInteger('folder_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['short_url', 'folder_id']);
            $table->index('folder_id');
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
