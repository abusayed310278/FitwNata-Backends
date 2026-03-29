<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMeasurement extends Model
{
    protected $guarded=[''];

     protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
