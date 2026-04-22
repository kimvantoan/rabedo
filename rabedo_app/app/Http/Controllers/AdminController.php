<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Apply basic auth middleware if needed
    // public function __construct() { $this->middleware('auth'); }

    public function index(Request $request)
    {
        $query = Article::where('type', 'Admin');
        
        if (!auth()->user()->is_admin) {
            $query->where('user_id', auth()->id());
        }

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($date = $request->input('date')) {
            $query->whereDate('created_at', $date);
        }

        $sortColumn = $request->input('sort', 'created_at');
        $sortDirection = $request->input('dir', 'desc');
        
        if (in_array($sortColumn, ['created_at', 'views', 'title'])) {
            $query->orderBy($sortColumn, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $articles = $query->paginate(15)->appends($request->all())->onEachSide(1);

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
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'thumbnail' => 'required_without:existing_thumbnail|nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'existing_thumbnail' => 'required_without:thumbnail|nullable|string',
        ], [
            'thumbnail.required_without' => 'Bạn phải tải lên hoặc chọn ảnh đại diện (thumbnail) từ thư viện.',
            'existing_thumbnail.required_without' => 'Bạn phải tải lên hoặc chọn ảnh đại diện (thumbnail) từ thư viện.',
        ]);

        $article = new Article;
        $article->title = $validated['title'];
        $article->description = $validated['description'] ?? null;
        // Generate a random slug fallback, or create proper slug function
        $article->slug = \Illuminate\Support\Str::slug($validated['title']) . '-' . time();
        $article->content = $validated['content'] ?? '';
        $article->type = 'Admin';
        $fakeAuthors = [
            'Arthur Pendelton', 'George Harrington', 'James Kensington', 'William Ashford',
            'Oliver Croft', 'Benjamin Sterling', 'Harry Davies', 'Thomas Redcliff',
            'Samuel Kingsley', 'Jack Montgomery', 'Amelia Thorne', 'Olivia Blackwood',
            'Eleanor Stanhope', 'Charlotte Bradley', 'Emily Fairburn', 'Isla Chambers',
            'Poppy Lancaster', 'Ava Pemberton', 'Isabella Carlisle', 'Jessica Whitmore'
        ];
        $article->author = $fakeAuthors[array_rand($fakeAuthors)];
        $article->user_id = \Illuminate\Support\Facades\Auth::id();

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('thumbnails', $filename, 'public');
            $article->thumbnail = '/storage/' . $path;
        } elseif ($request->filled('existing_thumbnail')) {
            $article->thumbnail = $request->existing_thumbnail;
        }
        $article->created_at = now();
        $article->updated_at = now();
        $article->save();

        return redirect()->route('admin.dashboard')->with('success', 'Article saved successfully.');
    }

    public function edit($id)
    {
        $query = Article::where('id', $id)
            ->where('type', 'Admin');
            
        if (!auth()->user()->is_admin) {
            $query->where('user_id', auth()->id());
        }
            
        $article = $query->firstOrFail();

        return view('admin.editor', compact('article'));
    }

    public function update(Request $request, $id)
    {
        $query = Article::where('id', $id)
            ->where('type', 'Admin');
            
        if (!auth()->user()->is_admin) {
            $query->where('user_id', auth()->id());
        }
            
        $article = $query->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'thumbnail' => 'required_without:existing_thumbnail|nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'existing_thumbnail' => 'required_without:thumbnail|nullable|string',
        ], [
            'thumbnail.required_without' => 'Bạn phải tải lên hoặc chọn ảnh đại diện (thumbnail) từ thư viện.',
            'existing_thumbnail.required_without' => 'Bạn phải tải lên hoặc chọn ảnh đại diện (thumbnail) từ thư viện.',
        ]);

        $article->title = $validated['title'];
        $article->description = $validated['description'] ?? null;
        $article->content = $validated['content'] ?? '';
        $article->type = 'Admin';

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('thumbnails', $filename, 'public');
            $article->thumbnail = '/storage/' . $path;
        } elseif ($request->filled('existing_thumbnail')) {
            $article->thumbnail = $request->existing_thumbnail;
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

    public function getMedia(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 40;

        $thumbnailsPath = storage_path('app/public/thumbnails');
        $uploadsPath = storage_path('app/public/uploads');

        $thumbnails = \Illuminate\Support\Facades\File::isDirectory($thumbnailsPath) 
            ? \Illuminate\Support\Facades\File::files($thumbnailsPath) : [];
            
        $uploads = \Illuminate\Support\Facades\File::isDirectory($uploadsPath) 
            ? \Illuminate\Support\Facades\File::files($uploadsPath) : [];
        
        $allFiles = array_merge($thumbnails, $uploads);
        
        // Sort by newest first (descending modification time)
        usort($allFiles, function($a, $b) {
            return $b->getMTime() <=> $a->getMTime();
        });

        $total = count($allFiles);
        $totalPages = (int) ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        $pagedFiles = array_slice($allFiles, $offset, $perPage);
        
        $urls = [];
        foreach ($pagedFiles as $file) {
            $folder = basename(dirname($file->getPathname()));
            $urls[] = '/storage/' . $folder . '/' . $file->getFilename();
        }

        return response()->json([
            'data' => $urls,
            'current_page' => (int) $page,
            'last_page' => $totalPages,
            'has_more' => $page < $totalPages
        ]);
    }

    public function destroy($id)
    {
        $query = Article::where('id', $id)
            ->where('type', 'Admin');
            
        if (!auth()->user()->is_admin) {
            $query->where('user_id', auth()->id());
        }
            
        $article = $query->firstOrFail();

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
