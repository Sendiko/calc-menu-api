<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::all();
        return response()->json([
            "status" => 200,
            "message" => "data retrieved successfully!",
            "orders" => $orders
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = $request->validate([
            "table_number" => "required|string|max:255",
            "menu_ids" => "required|array",
        ]);

        $emp = auth()->user();
        $restaurant = Restaurant::find($emp->restaurant_id);
        $menu_ids = json_encode($validator["menu_ids"]); // Convert array to JSON
    
        $menu_data = [];
        $total_price = 0;
    
        foreach ($validator["menu_ids"] as $id) {
            $menu = Menu::find($id);
            $menu_data[] = [
                "menu_name" => $menu->name,
                "menu_price" => $menu->price
            ];
            $total_price += $menu->price;
        }

        function generateTransactionId() {
            $today = date('Ymd');
            $id = $today . Str::random(6);
            return $id;
        }

        $order = Order::create([
            "transaction_id" => generateTransactionId(),
            "table_number" => $validator["table_number"],
            "restaurant_name" => $restaurant->name,
            "total_price" => $total_price,
            "payed" => 0,
            "menu_ids" => $menu_ids 
        ]);
    
        return response()->json([
            "status" => 201,
            "message" => "data stored successfully!",
            "order" => $order,
            "menu_data" => $menu_data,
        ], 201);
    }
        

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::findOrFail($id);
        
        $menuCounts = []; // Associative array to store menu counts
        
        foreach(json_decode($order->menu_ids) as $menu_id) {
            $menu = Menu::find($menu_id);
            
            // If the menu is not in the array, add it with a count of 1
            if (!isset($menuCounts[$menu->name])) {
                $menuCounts[$menu->name] = [
                    "menu_name" => $menu->name,
                    "menu_price" => $menu->price,
                    "amount" => 1
                ];
            } else {
                // If the menu is already in the array, increment the count
                $menuCounts[$menu->name]['amount']++;
            }
        }
        
        $menu_data = array_values($menuCounts); // Convert associative array to indexed array
        
        return response()->json([
            "status" => 200,
            "message" => "data retrieved successfully!",
            "order" => $order,
            "menus" => $menu_data
        ], 200);
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();

        if (!$restaurant = Restaurant::where("email", $user->email)->first()) {
            return response()->json([
                "status" => 403,
                "message" => "you're not authorized"
            ], 403);
        }

        $validator = $request->validate([
            "table_number" => "string",
            "payed" => "tinyInterger",
            "menu_ids" => "array"
        ]);

        $menu_ids = json_encode($validator["menu_ids"]);
        $order = Order::findOrFail($id);

        $menuCounts = []; // Associative array to store menu counts
        
        $order->update([
            "table_number" => $validator["table_number"] ?? $order->table_number,
            "payed" => $validator["payed"] ?? $order->payed,
            "menu_ids" => $menu_ids,
        ]);
        
        foreach(json_decode($order->menu_ids) as $menu_id) {
            $menu = Menu::find($menu_id);
            
            // If the menu is not in the array, add it with a count of 1
            if (!isset($menuCounts[$menu->name])) {
                $menuCounts[$menu->name] = [
                    "menu_name" => $menu->name,
                    "menu_price" => $menu->price,
                    "amount" => 1
                ];
            } else {
                // If the menu is already in the array, increment the count
                $menuCounts[$menu->name]['amount']++;
            }
        }

        $menu_data = array_values($menuCounts); // Convert associative array to indexed array

        return response()->json([
            "status" => 200,
            "message" => "data updated successfully!",
            "order" => $order,
            "menus" => $menu_data,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json([
            "status" => 200,
            "data successfully deletd!"
        ]);
    }
}
