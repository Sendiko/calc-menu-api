<?php

namespace App\Http\Controllers;

use App\Http\Requests\API\CreateEmployeeRequest;
use App\Http\Requests\API\LoginRequest;
use App\Http\Requests\API\RegisterRequest;
use App\Http\Requests\API\UploadProfileRequest;
use App\Models\Employee;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    function login(LoginRequest $request)
    {
        $validator = $request->validated();

        $restaurant = Restaurant::where("email", $validator["email"])->firstOrFail();
        if (!$restaurant || !Hash::check($validator["password"], $restaurant->password)) {
            return response()->json([
                "status" => 401,
                "message" => "$restaurant->name gagal login, mohon cek kembali data",
                "token" => null,
            ], 401);
        } else {
            $token = $restaurant->createToken("auth_token")->plainTextToken;
            return response()->json([
                "status" => 200,
                "message" => "$restaurant->name logged in successfully",
                "token" => $token
            ]);
        }
    }

    function register(RegisterRequest $request)
    {

        $validator = $request->validated();

        $restaurant = Restaurant::create([
            "name" => $validator["name"],
            "address" => $validator["address"],
            "phone_contact" => $validator["phone_contact"],
            "email" => $validator["email"],
            "password" => Hash::make($validator["password"]),
            "address" => $validator["address"],
            "phone_contact" => $validator["phone_contact"]
        ]);

        return response()->json([
            "status" => 201,
            "message" => "$restaurant->name registered successfully",
        ], 201);
    }

    function logout()
    {
        auth("sanctum")->user()->tokens()->delete();
        return response()->json([
            "status" => 200,
            "message" => "Logged out!",
            "token" => "null",
            "token_type" => "null"
        ]);
    }

    function createEmployeeAccount(CreateEmployeeRequest $request) 
    {

        $validator = $request->validated();

        function generateEmployeeId() {
            $today = date('Ymd');
            $id = $today . Str::random(6);
            return $id;
        }

        $employee = Employee::create([
            "employee_id" => generateEmployeeId(),
            "name" => $validator["name"],
            "email" => $validator["email"],
            "password" => Hash::make($validator["password"]),
            "restaurant_id" => $validator["restaurant_id"]
        ]);

        return response()->json([
            "status" => 201,
            "message" => "$employee->name account is created!",
            "account" => $employee
        ], 201);
    }

    function uploadProfile(UploadProfileRequest $request) 
    {

        $validator = $request->validated();

        $user = auth()->user();

        if (!$restaurant = Restaurant::where("email", $user->email)->first()) {
            return response()->json([
                "status" => 403,
                "message" => "you're not authorized"
            ], 403);
        }

        if($request->hasFile("image")){
            $image = $request->file("image");
            $fileName = $image->hashName();
            $image->storeAs("public/images/restaurant/", $fileName);
            $imageUrl = url("storage/images/restaurant/" . $fileName);  

            $restaurant->update([
                "imageUrl" => $imageUrl
            ]);
        }

        return response()->json([
            "status" => 200,
            "message" => "profile picture updated!",
            "imageUrl" => $imageUrl
        ]);
    }

    function getProfile() {
        $id = auth()->user();
        $resto = Restaurant::findOrfail($id);

        return response()->json([
            "status" => 200,
            "message" => "data sent successfully!",
            "restaurant" => $resto
        ]);
    }

}
