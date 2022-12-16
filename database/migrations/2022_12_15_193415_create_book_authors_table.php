<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Models\Book;
use \App\Models\BookAuthors;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_authors', function (Blueprint $table) {
            $table->comment('Storing the books authors');
            $table->engine='InnoDb';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->increments('ba_id');
            $table->unsignedInteger('book_id');
            $table->foreign('book_id')->references('book_id')->on('books');
            $table->unsignedInteger('author_id');
            $table->foreign('author_id')->references('author_id')->on('authors');
            $table->timestamp('create_date')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_authors');
    }
};
