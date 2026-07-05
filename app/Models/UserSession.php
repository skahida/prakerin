<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    protected $table = 'sessions';

    protected $fillable = ['user_id', 'ip_address', 'user_agent', 'payload', 'last_activity'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
