<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImMessage extends Model
{
    protected $fillable = ['conversation_id', 'user_id', 'type', 'content'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'conversationId' => $this->conversation_id,
            'userId' => $this->user_id,
            'type' => $this->type,
            'content' => $this->content,
            'nickname' => $this->user?->nickname,
            'avatar' => $this->user?->avatar,
            'createdAt' => $this->created_at?->toDateTimeString(),
        ];
    }
}
