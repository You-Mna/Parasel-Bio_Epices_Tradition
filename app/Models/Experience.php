<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_name', 'content', 'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];
}

