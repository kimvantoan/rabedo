<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::query();

        if ($request->has('type')) {
            $query->where('type', $request->query('type'));
        }

        // Search text by title
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->query('search') . '%');
        }

        return response()->json($query->orderBy('created_at', 'desc')->paginate(10));
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240', // Max 10MB
        ]);

        $path = $request->file('image')->store('content-images', 'public');
        return response()->json([
            'url' => Storage::url($path)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'thumbnail' => 'nullable|file|image|max:5120', // MAX 5MB
        ]);

        $data = $request->except('thumbnail');
        $data['author'] = $request->user()->name ?? 'Admin';
        $data['type'] = 'Admin';

        // Xử lý upload ảnh bìa (thumbnail)
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $data['thumbnail'] = Storage::url($path);
        }

        $article = Article::create($data);

        return response()->json([
            'message' => 'Đã lưu bài viết thành công!',
            'data' => $article
        ], 201);
    }

    public function show(Article $article)
    {
        return response()->json($article);
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'thumbnail' => 'nullable|file|image|max:5120',
        ]);

        $data = $request->except('thumbnail');

        if ($request->hasFile('thumbnail')) {
            // Xóa ảnh cũ trên bộ nhớ
            if ($article->thumbnail) {
                $oldPath = str_replace('/storage/', '', $article->thumbnail);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $data['thumbnail'] = Storage::url($path);
        }

        $article->update($data);

        return response()->json([
            'message' => 'Đã cập nhật bài viết thành công!',
            'data' => $article
        ]);
    }

    public function destroy(Article $article)
    {
        if ($article->thumbnail) {
            $oldPath = str_replace('/storage/', '', $article->thumbnail);
            Storage::disk('public')->delete($oldPath);
        }
        $article->delete();
        
        return response()->json(['message' => 'Đã xóa bài viết khỏi hệ thống!']);
    }
}
