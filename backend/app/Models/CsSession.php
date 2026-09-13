<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CsSession extends Model
{
    protected $fillable = ['user_id', 'admin_id', 'status', 'last_message_at'];
    protected $casts = ['last_message_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(CsMessage::class);
    }
}
