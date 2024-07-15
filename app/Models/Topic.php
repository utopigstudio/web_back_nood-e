<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'description',
        'user_id',
        'comments_counter',
        'last_update',
    ];

    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }
}
