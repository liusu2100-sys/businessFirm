<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['title', 'content', 'type', 'is_top', 'status', 'sort'];
    protected $casts = ['is_top' => 'boolean'];

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'type' => $this->type,
            'isTop' => $this->is_top,
            'status' => $this->status,
            'sort' => $this->sort,
            'createdAt' => $this->created_at?->toDateTimeString(),
        ];
    }
}
