<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Apply basic auth middleware if needed
    // public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        $articles = Article::where('type', 'Admin')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.dashboard', compact('articles'));
    }

    public function editor()
    {
        return view('admin.editor');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $article = new Article;
        $article->title = $validated['title'];
        // Generate a random slug fallback, or create proper slug function
        $article->slug = \Illuminate\Support\Str::slug($validated['title']) . '-' . time();
        $article->content = $validated['content'];
        $article->type = 'Admin';
        $fakeAuthors = [
            'Arthur Pendelton', 'George Harrington', 'James Kensington', 'William Ashford',
            'Oliver Croft', 'Benjamin Sterling', 'Harry Davies', 'Thomas Redcliff',
            'Samuel Kingsley', 'Jack Montgomery', 'Amelia Thorne', 'Olivia Blackwood',
            'Eleanor Stanhope', 'Charlotte Bradley', 'Emily Fairburn', 'Isla Chambers',
            'Poppy Lancaster', 'Ava Pemberton', 'Isabella Carlisle', 'Jessica Whitmore'
        ];
        $article->author = $fakeAuthors[array_rand($fakeAuthors)];

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('thumbnails', $filename, 'public');
            $article->thumbnail = '/storage/' . $path;
        }
        $article->created_at = now();
        $article->updated_at = now();
        $article->save();

        return redirect()->route('admin.dashboard')->with('success', 'Article saved successfully.');
    }

    public function edit($id)
    {
        $article = Article::where('id', $id)
            ->where('type', 'Admin')
            ->firstOrFail();

        return view('admin.editor', compact('article'));
    }

    public function update(Request $request, $id)
    {
        $article = Article::where('id', $id)
            ->where('type', 'Admin')
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $article->title = $validated['title'];
        $article->content = $validated['content'];
        $article->type = 'Admin';

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('thumbnails', $filename, 'public');
            $article->thumbnail = '/storage/' . $path;
        }

        $article->updated_at = now();
        $article->save();

        return redirect()->route('admin.dashboard')->with('success', 'Article updated successfully.');
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads', $filename, 'public');
            
            return response()->json([
                'url' => '/storage/' . $path
            ]);
        }
        
        return response()->json(['error' => ['message' => 'Lỗi tải ảnh lên.']], 400);
    }

    public function destroy($id)
    {
        $article = Article::where('id', $id)
            ->where('type', 'Admin')
            ->firstOrFail();

        // Optionally, remove thumbnail from disk
        if ($article->thumbnail) {
            $relativePath = str_replace('/storage/', '', $article->thumbnail);
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($relativePath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($relativePath);
            }
        }

        $article->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Đã xoá bài viết thành công.');
    }
}
