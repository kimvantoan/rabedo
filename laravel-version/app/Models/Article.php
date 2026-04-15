<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    // The Next.js DB used 'articles' table (default for Laravel is 'articles')
    // but the Next.js schema maps dates to timestamps without timezone.
    public $timestamps = true;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'thumbnail',
        'author',
        'type',
    ];
    
    // We treat 'created_at' and 'updated_at' as standard timestamps
}
