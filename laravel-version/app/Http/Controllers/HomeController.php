<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Fetch public articles (excluding Admin types or as per the old Next.js logic)
        $articles = Article::where('type', '!=', 'Admin')
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);
                           
        return view('home', compact('articles'));
    }
}
