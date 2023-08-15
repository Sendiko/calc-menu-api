<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Model
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [
        "employee_id",
        "name",
        "email",
        "password",
        "restaurant_id",
        "imageUrl"
    ];
}
