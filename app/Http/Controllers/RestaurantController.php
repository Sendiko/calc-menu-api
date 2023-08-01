<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RestaurantController extends Controller
{
    function login(Request $request)
    {
        $validator = $request->validate([
            "email" => "required|string",
            "password" => "required|string|min:6"
        ]);

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

    function register(Request $request)
    {

        $validator = $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|string",
            "password" => "required|string|min:6|confirmed"
        ]);

        $restaurant = Restaurant::create([
            "name" => $validator["name"],
            "email" => $validator["email"],
            "password" => Hash::make($validator["password"]),
        ]);

        return response()->json([
            "status" => 201,
            "message" => "Restaurant registered successfully",
        ], 201);
    }

    function logout()
    {
        auth("sanctum")->user()->tokens()->delete();
        return response()->json([
            "status" => 200,
            "message" => "berhasil logout",
            "token" => "null",
            "token_type" => "null"
        ]);
    }
}