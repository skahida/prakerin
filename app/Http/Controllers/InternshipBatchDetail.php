<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternshipBatchDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'internship_batch_id',
        'mentor_id',
        'place_code',
    ];

    public function batch()
    {
        return $this->belongsTo(InternshipBatch::class, 'internship_batch_id');
    }

    public function mentor()
    {
        return $this->belongsTo(Mentor::class, 'mentor_id');
    }

    public function place()
    {
        return $this->belongsTo(InternshipPlace::class, 'place_code');
    }
}
