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

        $article->increment('views');

        $query = Article::where('id', '!=', $article->id);

        if ($article->type === 'Admin') {
            $query->where('type', 'Admin');
        } else {
            $query->where(function($q) {
                $q->where('type', '!=', 'Admin')
                  ->orWhereNull('type');
            });
        }

        $relatedArticles = $query->inRandomOrder()
                                 ->take(6)
                                 ->get();

        return view('articles.show', compact('article', 'relatedArticles'));
    }
}
