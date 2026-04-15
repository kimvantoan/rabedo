<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function show($idOrSlug)
    {
        // Try finding by ID first, then fallback to slug
        $article = Article::where('id', $idOrSlug)
                          ->orWhere('slug', $idOrSlug)
                          ->firstOrFail();

        return view('articles.show', compact('article'));
    }
}
