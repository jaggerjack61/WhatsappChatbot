<?php

use App\Http\Controllers\CleanWebhookController;
use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::get('/webhook/',[WebhookController::class,'webhookSetup']);
Route::post('/webhook/',[WebhookController::class,'webhookReceiver']);
Route::get('/webhook/v2/',[CleanWebhookController::class,'webhookSetup']);
Route::post('/webhook/v2/',[CleanWebhookController::class,'webhookReceiver']);
