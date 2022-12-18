<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\ApiController\PublisherController;

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
Route::group(['prefix' => 'v1'], function(){
    Route::post('/books', [PublisherController::class, 'storeBookInfo']);

    Route::get('/books', [PublisherController::class, 'getBooksList']);
    Route::get('/books/{bookId}', [PublisherController::class, 'getBookById']);
    Route::delete('/books/{bookId}', [PublisherController::class, 'deleteBookById']);
    Route::put('/books/{bookId}', [PublisherController::class, 'updateBookInfoById']);
});

