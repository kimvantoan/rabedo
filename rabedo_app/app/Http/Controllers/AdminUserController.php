<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::withCount('articles')
            ->orderBy('id', 'desc')
            ->paginate(15)->onEachSide(1);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.index')->with('success', 'Tạo tài khoản thành công!');
    }

    public function show($id)
    {
        $user = User::withCount('articles')->findOrFail($id);
        $articles = Article::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15)->onEachSide(1);
            
        return view('admin.users.show', compact('user', 'articles'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'max:50',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Cập nhật tài khoản thành công!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === \Illuminate\Support\Facades\Auth::id()) {
            return redirect()->route('users.index')->with('error', 'Bạn không thể xóa tài khoản đang đăng nhập!');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Đã xóa tài khoản thành công!');
    }
}
