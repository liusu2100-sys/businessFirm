<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'user_id', 'order_id', 'title', 'content', 'images', 'status', 'reply',
    ];

    protected $casts = ['images' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'orderId' => $this->order_id,
            'title' => $this->title,
            'content' => $this->content,
            'images' => $this->images,
            'status' => $this->status,
            'reply' => $this->reply,
            'createdAt' => $this->created_at?->toDateTimeString(),
        ];
    }
}
