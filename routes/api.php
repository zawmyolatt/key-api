<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ObjectController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Object Endpoints
Route::group([ 'prefix' => 'object', 'as' => 'object.'], function ($router) {

    Route::get('/get_all_records', [ObjectController::class, 'index'])->name('get_all_records');
    Route::get('/{key}', [ObjectController::class, 'show'])->name('show');
    Route::post('/', [ObjectController::class, 'store'])->name('store');

});
