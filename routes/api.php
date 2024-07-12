<?php

use App\Http\Controllers\API\AdvertisementAPIController;
use App\Http\Controllers\API\GetBanner;
use App\Http\Controllers\API\GetDatabuyAPIController;
use App\Http\Controllers\API\LoadingAPI;
use App\Http\Controllers\API\MiniAPIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PostAPIController;
use App\Http\Controllers\API\UserAPIController;
use App\Http\Controllers\API\ZaloAPIController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/





Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/loading-image', [LoadingAPI::class, 'getLoadingImage']);
//Mini-app
Route::get('/miniapp', [MiniAPIController::class, 'getMinigame']);
//Blogs
Route::get('/blog/{id}', [PostAPIController::class, 'Blog']);
Route::get('/blog', [PostAPIController::class, 'BlogALL']);
//Advertisements
Route::get('/advertisement/{id}', [AdvertisementAPIController::class, 'Advertisement']);
Route::get('/advertisement', [AdvertisementAPIController::class, 'AllAdvertisements']);
//Banner
Route::get('/banner', [GetBanner::class, 'getBanner']);
//User 
Route::post('/register', [UserAPIController::class, 'register']);
Route::post('/login', [UserAPIController::class, 'login']);
Route::get('/profile', [UserAPIController::class, 'getProfile']);
Route::post('/update', [UserAPIController::class, 'updateProfile']);
Route::post('/updateNumberSpin' ,[UserAPIController::class, 'updateNumberSpin']);
Route::middleware('auth:sanctum')->post('/logout', [UserAPIController::class, 'logout']);
Route::middleware('auth:sanctum')->group(function () {
    
});
Route::post('/savewebhook ', [GetDatabuyAPIController::class, 'getDataBuy']);
Route::post('/webhook ', [GetDatabuyAPIController::class, 'checkSignature']);    
Route::post('/signature', [GetDatabuyAPIController::class, 'generateSignature']);