<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CsMessage extends Model
{
    protected $fillable = ['cs_session_id', 'user_id', 'type', 'content', 'is_staff'];
    protected $casts = ['is_staff' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'sessionId' => $this->cs_session_id,
            'userId' => $this->user_id,
            'type' => $this->type,
            'content' => $this->content,
            'isStaff' => $this->is_staff,
            'nickname' => $this->user?->nickname,
            'createdAt' => $this->created_at?->toDateTimeString(),
        ];
    }
}
