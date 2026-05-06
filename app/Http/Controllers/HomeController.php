<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::where('type', 'Admin');
        $searchTerm = $request->input('q');

        if (!empty($searchTerm)) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('content', 'like', '%' . $searchTerm . '%');
            });
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(30)->appends($request->all())->onEachSide(1);
                           
        return view('home', compact('articles', 'searchTerm'));
    }
}
