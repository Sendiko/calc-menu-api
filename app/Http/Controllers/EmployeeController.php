<?php

namespace App\Http\Controllers;

use App\Http\Requests\API\LoginRequest;
use App\Http\Requests\API\UploadProfileRequest;
use App\Models\Employee;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    function login(LoginRequest $request) {
        $validator = $request->validated();

        $employee = Employee::where("email", $validator["email"])->firstOrFail();
        if (!$employee || !Hash::check($validator["password"], $employee->password)) {
            return response()->json([
                "status" => 401,
                "message" => "$employee->name gagal login, mohon cek kembali data",
                "token" => null,
            ], 401);
        } else {
            $token = $employee->createToken("auth_token")->plainTextToken;
            return response()->json([
                "status" => 200,
                "message" => "$employee->name logged in successfully",
                "token" => $token,
                "user" => $employee
            ]);
        }
    }

    function logout(Request $request) {
        auth("sanctum")->user()->tokens()->delete();
        return response()->json([
            "status" => 200,
            "message" => "berhasil logout",
            "token" => "null",
            "token_type" => "null"
        ]);
    }

    function uploadProfile(UploadProfileRequest $request) 
    {

        $validator = $request->validated();

        $user = auth()->user();

        if (!$employee = Employee::where("email", $user->email)->first()) {
            return response()->json([
                "status" => 403,
                "message" => "you're not authorized"
            ], 403);
        }

        if($request->hasFile("image")){
            $image = $request->file("image");
            $fileName = $image->hashName();
            $image->storeAs("public/images/employee/", $fileName);
            $imageUrl = url("storage/images/employee/" . $fileName);  

            $employee->update([
                "imageUrl" => $imageUrl
            ]);
        }

        return response()->json([
            "status" => 200,
            "message" => "profile picture updated!",
            "imageUrl" => $imageUrl
        ]);
    }

    function getEmployeeList() {
        $user = auth()->user();

        if (!$restaurant = Restaurant::where("email", $user->email)->first()) {
            return response()->json([
                "status" => 403,
                "message" => "you're not authorized"
            ], 403);
        }

        $employees = Employee::all();

        return response()->json([
            "status" => 200,
            "message" => "data sent successfully!",
            "employees" => $employees
        ]);
        
    }

}
