<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminChapterController extends Controller
{
    public function create($articleId)
    {
        $article = Article::findOrFail($articleId);
        // Suggest the next chapter number
        $nextChapter = $article->chapters()->max('chapter_number') + 1;
        return view('admin.chapters.editor', compact('article', 'nextChapter'));
    }

    public function store(Request $request, $articleId)
    {
        $article = Article::findOrFail($articleId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'chapter_number' => 'required|integer|min:1',
            'content' => 'required|string',
        ]);

        $chapter = new Chapter();
        $chapter->article_id = $article->id;
        $chapter->chapter_number = $validated['chapter_number'];
        $chapter->title = $validated['title'];
        $chapter->slug = Str::slug($validated['title']);
        $chapter->content = $validated['content'];
        $chapter->save();

        return redirect()->to(route('admin.edit', $article->id) . '#chapters-section')->with('success', 'Chương mới đã được thêm thành công.');
    }

    public function edit($id)
    {
        $chapter = Chapter::with('article')->findOrFail($id);
        $article = $chapter->article;
        return view('admin.chapters.editor', compact('chapter', 'article'));
    }

    public function update(Request $request, $id)
    {
        $chapter = Chapter::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'chapter_number' => 'required|integer|min:1',
            'content' => 'required|string',
        ]);

        $chapter->chapter_number = $validated['chapter_number'];
        $chapter->title = $validated['title'];
        $chapter->slug = Str::slug($validated['title']);
        $chapter->content = $validated['content'];
        $chapter->save();

        return redirect()->to(route('admin.edit', $chapter->article_id) . '#chapters-section')->with('success', 'Chương đã được cập nhật thành công.');
    }

    public function destroy($id)
    {
        $chapter = Chapter::findOrFail($id);
        $articleId = $chapter->article_id;
        $chapter->delete();

        return redirect()->to(route('admin.edit', $articleId) . '#chapters-section')->with('success', 'Đã xoá chương thành công.');
    }
}
