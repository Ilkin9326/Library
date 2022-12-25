<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\ApiController\PublisherController;
use \App\Http\Controllers\ApiController\TestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['prefix' => 'v1', 'middleware' => ['verifyApiKey']], function () {

    Route::controller(PublisherController::class)->group(function () {
        Route::post('/books', 'storeBookInfo');

        Route::get('/books', 'getBooksList');
        Route::get('/books/{bookId}', 'getBookById');
        Route::delete('/books/{bookId}', 'deleteBookById')->where('bookId', '[0-9]+');
        Route::put('/books/{bookId}', 'updateBookInfoById');
        Route::delete('/delete/{id}', 'deleteById')->where('id', '[0-9]+')->middleware('ValidateSignature');
    });



});

