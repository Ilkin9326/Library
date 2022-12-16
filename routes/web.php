<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $authors = \Illuminate\Support\Facades\DB::table('book_authors as ba')
        ->join('books as b', 'ba.book_id', '=', 'b.book_id')
        ->join('authors as a', 'ba.author_id', '=', 'a.author_id')
        ->join('book_publishers as bpp', 'ba.book_id', '=', 'bpp.book_id')
        ->join('publisher as p', 'bpp.publisher_id', '=', 'p.publisher_id')
        ->select('b.title as book_title', 'a.name', 'a.surname', 'p.name as publisher_name')
        ->get();
    return view('library', ['name' => 'Samantha', 'data'=>$authors]);
});
