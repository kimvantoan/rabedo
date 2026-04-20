<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    //
    protected $fillable = [
        'article_id',
        'chapter_number',
        'title',
        'slug',
        'content',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
