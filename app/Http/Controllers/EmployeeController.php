<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    function login(Request $request) {
        $validator = $request->validate([
            "email" => "required|string",
            "password" => "required|string|min:6"
        ]);

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
                "token" => $token
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

}
