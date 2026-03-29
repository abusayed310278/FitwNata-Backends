<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ChatMessage extends Model
{
    protected $guarded = [];

    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    protected function attachment(): Attribute
    {
        return Attribute::make(
            get: function ($value) 
            {
                if (empty($value)) {
                    return null;
                }

                // If already a full URL (starts with http or https), return as-is
                if (preg_match('/^https?:\/\//', $value)) {
                    return $value;
                }

                // Otherwise, it's a relative path from /public, so prefix with asset()
                return asset($value);
            }
        );
    }
}
