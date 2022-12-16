<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Models\Book;
use \App\Models\Publisher;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_publishers', function (Blueprint $table) {
            $table->comment('Storing the books publisher');
            $table->engine='InnoDb';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->increments('bp_id');
            $table->unsignedInteger('book_id');
            $table->foreign('book_id')->references('book_id')->on('books');
            $table->unsignedInteger('publisher_id');
            $table->foreign('publisher_id')->references('publisher_id')->on('publisher');
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
        Schema::dropIfExists('book_publishers');
    }
};
