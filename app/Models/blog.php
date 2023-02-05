<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function categories()
    {
        return $this->belongsToMany(category::class, 'blog_categories');
    }

    public function comments()
    {
        return $this->belongsToMany(comment::class, 'blog_comments');
    }
}
