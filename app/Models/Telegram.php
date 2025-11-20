<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Telegram extends Model
{
    use HasFactory;

    // Tentukan kolom yang bisa diisi (mass assignable)
    protected $fillable = [
        'bot_token',
        'message',
        'username',
    ];
}
