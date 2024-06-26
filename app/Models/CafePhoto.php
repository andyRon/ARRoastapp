<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CafePhoto extends Model
{
    use HasFactory;

    protected $table = 'cafes_photos';

    public function cafe()
    {
        return $this->belongsTo(Cafe::class, 'cafe_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }
}
