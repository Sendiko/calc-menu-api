<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::all();
        return response()->json([
            "status" => 200,
            "message" => "data sent successfully!",
            "menus" => $menus
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = $request->validate([
            "name" => "required|string|max:60",
            "description" => "required|string|max:255",
            "category" => "required|string|in:food,beverage",
            "image" => "required|image|mimes:jpg,jpeg,png",
            "price" => "required|integer"
        ]);

        $user = auth()->user();

        if (!$restaurant = Restaurant::where("email", $user->email)->first()) {
            return response()->json([
                "status" => 403,
                "message" => "you're not authorized"
            ], 403);
        }

        if ($request->hasFile("image")) {
            $image = $request->file("image");
            $fileName = $image->hashName();
            $image->storeAs("public/images/", $fileName);
            $imageUrl = url("storage/images/menu/" . $fileName);

            $menu = Menu::create([
                "name" => $validator["name"],
                "description" => $validator["description"],
                "category" => $validator["category"],
                "imageUrl" => $imageUrl,
                "price" => $validator["price"]
            ]);

            return response()->json([
                "status" => 200,
                "messages" => "data stored successfully!",
                "menu" => $menu
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            "name" => "string|max:60",
            "description" => "string|max:255",
            "category" => "string|in:food,beverage",
            "image" => "image|mimes:jpg,jpeg,png",
            "price" => "integer"
        ]);

        $user = auth()->user();

        if (!$restaurant = Restaurant::where("email", $user->email)->first()) {
            return response()->json([
                "status" => 403,
                "message" => "you're not authorized"
            ], 403);
        }

        $menu = Menu::findorFail($id);

        $menu->update([
            "name" => $validator["name"] ?? $menu->name,
            "description" => $validator["description"] ?? $menu->description,
            "category" => $validator["category"] ?? $menu->category,
            "price" => $validator["price"] ?? $menu->price,
        ]);

        return response()->json([
            "status" => 200,
            "messages" => "data stored successfully!",
            "menu" => $menu
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();


        return response()->json([
            "status" => 200,
            "messages" => "menu deleted successfully!",
        ], 200);
    }

    public function updateImage(Request $request)
    {
        $validator = $request->validate([
            "id" => "required|string",
            "image" => "image|mimes:jpg,jpeg,png"
        ]);
    
        $user = auth()->user();
    
        if (!$restaurant = Restaurant::where("email", $user->email)->first()) {
            return response()->json([
                "status" => 403,
                "message" => "you're not authorized"
            ], 403);
        }
    
        $menu = Menu::findorFail($validator["id"]);
    
        if ($request->hasFile("image")) {
            $image = $request->file("image");
            $fileName = $image->hashName();
            $oldFileName = $menu->imageUrl;
            $oldImagePath = public_path("public/images/menu/" . $oldFileName);
    
            File::delete($oldImagePath);
    
            $image->storeAs("public/images/menu/", $fileName);
            $imageUrl = url("storage/images/menu/" . $fileName);
    
            $menu->update([
                "imageUrl" => $imageUrl,
            ]);
        } else {
            $menu->imageUrl = null;
        }
    
        return response()->json([
            "status" => 200,
            "messages" => "image updated successfully!",
            "menu" => $menu->imageUrl
        ], 200);
    }
    
}
