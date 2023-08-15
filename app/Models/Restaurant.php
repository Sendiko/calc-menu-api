<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Restaurant extends Model
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [
        "name",
        "email",
        "password",
        "address",
        "phone_contact",
        "imageUrl"
    ];

}
