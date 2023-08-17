<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        "transaction_id",
        "restaurant_name",
        "table_number",
        "total_price",
        "payed",
        "menu_ids",
        "menu_notes"
    ];
}
