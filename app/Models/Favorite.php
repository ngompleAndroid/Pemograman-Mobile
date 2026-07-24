<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $table = 'favorites';

    protected $fillable = [
        'user_id', // 👈 SESUAIKAN DENGAN TABEL PHPMYADMIN
        'restaurant_id',
        'name',
        'address',
        'rating',
        'image_url',
        'is_favorite',
        'note',
    ];
}