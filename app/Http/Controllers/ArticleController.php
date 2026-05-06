<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function show($idOrSlug)
    {
        $article = Article::with('chapters')->where('id', $idOrSlug)
                          ->orWhere('slug', $idOrSlug)
                          ->firstOrFail();

        $article->increment('views');
        \App\Models\ArticleView::firstOrCreate(
            ['article_id' => $article->id, 'view_date' => now()->toDateString()],
            ['views' => 0]
        )->increment('views');
        
        $currentChapter = null;
        if ($article->chapters->count() > 0) {
            $currentChapter = $article->chapters->first();
        }

        $relatedArticles = Article::where('id', '!=', $article->id)
                                  ->where('type', $article->type)
                                  ->inRandomOrder()
                                  ->take(6)
                                  ->get();

        return view('articles.show', compact('article', 'relatedArticles', 'currentChapter'));
    }

    public function showChapter($idOrSlug, $chapterNumber)
    {
        $article = Article::with('chapters')->where('id', $idOrSlug)
                          ->orWhere('slug', $idOrSlug)
                          ->firstOrFail();

        $currentChapter = $article->chapters->where('chapter_number', $chapterNumber)->firstOrFail();

        $article->increment('views');
        \App\Models\ArticleView::firstOrCreate(
            ['article_id' => $article->id, 'view_date' => now()->toDateString()],
            ['views' => 0]
        )->increment('views');
        
        $relatedArticles = Article::where('id', '!=', $article->id)
                                  ->where('type', $article->type)
                                  ->inRandomOrder()
                                  ->take(6)
                                  ->get();

        return view('articles.show', compact('article', 'relatedArticles', 'currentChapter'));
    }
}
