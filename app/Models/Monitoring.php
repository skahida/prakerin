<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitoring extends Model
{
    protected $fillable = [
        'mentor_id',
        'place_code',
        'photo',
        'status',
        'check_latitude',
        'check_longitude',
        'check_location_link',
    ];

    public function internshipPlace()
    {
        return $this->belongsTo(InternshipPlace::class, 'place_code', 'code');
    }

    // app/Models/Monitoring.php
    public function mentor()
    {
        return $this->belongsTo(\App\Models\Mentor::class, 'mentor_id', 'id');
    }
}
