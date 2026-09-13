<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['type', 'title', 'game_account_id', 'order_id', 'last_message_at'];
    protected $casts = ['last_message_at' => 'datetime'];

    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_participants')->withTimestamps()->withPivot('last_read_at');
    }

    public function messages()
    {
        return $this->hasMany(ImMessage::class);
    }

    public function gameAccount()
    {
        return $this->belongsTo(GameAccount::class);
    }
}
