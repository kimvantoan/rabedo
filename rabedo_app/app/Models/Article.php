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
        'description',
        'slug',
        'content',
        'thumbnail',
        'author',
        'type',
        'user_id',
    ];
    
    // We treat 'created_at' and 'updated_at' as standard timestamps

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    public function chapters()
    {
        return $this->hasMany(Chapter::class)->orderBy('chapter_number', 'asc');
    }
}
