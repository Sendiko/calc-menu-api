<?php

namespace App\Http\Controllers;

use App\Http\Requests\API\MenuUpdateRequest;
use App\Http\Requests\API\UpdateImageRequest;
use App\Http\Requests\MenuStoreRequest;
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
    public function store(MenuStoreRequest $request)
    {

        $validator = $request->validated();

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
            $image->storeAs("public/images/menu/", $fileName);
            $imageUrl = url("storage/images/menu/" . $fileName);

            $menu = Menu::create([
                "name" => $validator["name"],
                "description" => $validator["description"],
                "category" => $validator["category"],
                "imageUrl" => $imageUrl,
                "price" => $validator["price"],
                "restaurant_id" => $validator["restaurant_id"]
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
        $menus = Menu::where("restaurant_id", $id)->get();
        return response()->json([
            "status" => 200,
            "message" => "data sent successfully!",
            "menus" => $menus
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MenuUpdateRequest $request, $id)
    {
        $validator = $request->validated();

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
            "restaurant_id" => $validator["restaurant_id"] ?? $menu->restaurant_id
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
        $menu = Menu::findorFail($id);
        $menu->delete();

        return response()->json([
            "status" => 200,
            "messages" => "menu deleted successfully!",
        ], 200);
    }

    public function updateImage(UpdateImageRequest $request)
    {
        $validator = $request->validated();

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
