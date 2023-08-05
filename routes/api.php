<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Route;

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

Route::post("resto/login", [RestaurantController::class ,"login"]);
Route::post("resto/register", [RestaurantController::class ,"register"]);

Route::post("emp/login", [EmployeeController::class ,"login"]);

Route::middleware("auth:sanctum")->group(function () {
    Route::post("resto/logout", [RestaurantController::class, "logout"]);
    Route::post("emp/logout", [EmployeeController::class, "logout"]);
    Route::post("resto/create_employee", [RestaurantController::class, "createEmployeeAccount"]);
    Route::resource("menu", MenuController::class);
    Route::resource("order", OrderController::class);
    Route::post("menu/update_image/", [MenuController::class, "updateImage"]);
});
