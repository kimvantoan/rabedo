@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm_px-6 lg_px-8">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Admin Dashboard</h1>
        <div class="flex space-x-3">
            <button onclick="generateAi(this)" data-url="{{ route('admin.generate_ai') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-purple-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover_bg-purple-700 transition">
                ✨ Tự động tạo bài AI
            </button>
            <a href="{{ route('admin.editor') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover_bg-indigo-700 transition">
                + Viết bài mới
            </a>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover_bg-gray-50 transition">
                    Đăng xuất
                </button>
            </form>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 mb-8">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="{{ route('admin.dashboard') }}" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" aria-current="page">
                Quản lý Bài viết
            </a>
            @if(auth()->check() && auth()->user()->is_admin)
            <a href="{{ route('users.index') }}" class="border-transparent text-gray-500 hover_text-gray-700 hover_border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Quản lý Tài khoản
            </a>
            @endif
        </nav>
    </div>

    @if (session('success'))
    <div class="rounded-md bg-green-50 p-4 mb-6">
        <div class="flex">
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if (session('error'))
    <div class="rounded-md bg-red-50 p-4 mb-6">
        <div class="flex">
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="mb-4">
        <div class="sm_hidden">
            <label for="article_type" class="sr-only">Lọc bài viết</label>
            <select id="article_type" name="article_type" class="block w-full rounded-md border-gray-300 focus_border-indigo-500 focus_ring-indigo-500 py-2 pl-3 pr-10 text-base" onchange="window.location.href=this.value">
                <option value="{{ request()->fullUrlWithQuery(['article_type' => 'all', 'page' => 1]) }}" {{ request('article_type', 'all') == 'all' ? 'selected' : '' }}>Tất cả bài viết</option>
                <option value="{{ request()->fullUrlWithQuery(['article_type' => 'single', 'page' => 1]) }}" {{ request('article_type') == 'single' ? 'selected' : '' }}>Bài viết đơn</option>
                <option value="{{ request()->fullUrlWithQuery(['article_type' => 'chaptered', 'page' => 1]) }}" {{ request('article_type') == 'chaptered' ? 'selected' : '' }}>Bài có Chapters</option>
            </select>
        </div>
        <div class="hidden sm_block">
            <nav class="flex space-x-4" aria-label="Tabs">
                <a href="{{ request()->fullUrlWithQuery(['article_type' => 'all', 'page' => 1]) }}" class="{{ request('article_type', 'all') == 'all' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover_text-gray-700 hover_bg-gray-100' }} rounded-md px-3 py-2 text-sm font-medium transition-colors">
                    Tất cả bài viết
                </a>
                <a href="{{ request()->fullUrlWithQuery(['article_type' => 'single', 'page' => 1]) }}" class="{{ request('article_type') == 'single' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover_text-gray-700 hover_bg-gray-100' }} rounded-md px-3 py-2 text-sm font-medium transition-colors">
                    Bài viết đơn
                </a>
                <a href="{{ request()->fullUrlWithQuery(['article_type' => 'chaptered', 'page' => 1]) }}" class="{{ request('article_type') == 'chaptered' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover_text-gray-700 hover_bg-gray-100' }} rounded-md px-3 py-2 text-sm font-medium transition-colors">
                    Bài có Chapters
                </a>
            </nav>
        </div>
    </div>

    <div class="mb-4 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-wrap sm_flex-nowrap items-center gap-3">
            @if(request('article_type'))
            <input type="hidden" name="article_type" value="{{ request('article_type') }}">
            @endif
            <div class="relative flex-grow max-w-md w-full sm_w-auto">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm tiêu đề bài viết..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus_outline-none focus_placeholder-gray-400 focus_ring-1 focus_ring-indigo-500 focus_border-indigo-500 sm_text-sm">
            </div>
            <div class="relative flex-grow max-w-[200px] w-full sm_w-auto">
                <input type="date" name="date" value="{{ request('date') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white text-gray-700 focus_outline-none focus_ring-1 focus_ring-indigo-500 focus_border-indigo-500 sm_text-sm">
            </div>
            <div class="flex items-center gap-2 mt-2 sm_mt-0 w-full sm_w-auto">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover_bg-indigo-700 w-full sm_w-auto">Tìm kiếm</button>
                @if(request('search') || request('date'))
                <a href="{{ route('admin.dashboard') }}" class="inline-flex justify-center py-2 px-4 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white border border-gray-300 hover_bg-gray-50 whitespace-nowrap w-full sm_w-auto text-center">Xoá lọc</a>
                @endif
            </div>
        </form>
    </div>

    <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md_rounded-lg w-full">
        <table class="min-w-full divide-y divide-gray-300 bg-white">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">ID</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tiêu đề</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Biên tập viên</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 hidden sm_table-cell">Ngày tạo</th>
                    @if(auth()->check() && auth()->user()->is_admin)
                    <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">
                        @php
                        $viewSortDir = request('sort') === 'views' && request('dir') === 'desc' ? 'asc' : 'desc';
                        @endphp
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'views', 'dir' => $viewSortDir]) }}" class="group inline-flex items-center hover_text-indigo-600">
                            Lượt xem
                            <span class="ml-1 flex-none rounded bg-gray-100 text-gray-900 group-hover_bg-gray-200">
                                @if(request('sort') === 'views')
                                @if(request('dir') === 'asc')
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                                @else
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                @endif
                                @else
                                <svg class="h-4 w-4 text-gray-400 invisible group-hover_visible" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                @endif
                            </span>
                        </a>
                    </th>
                    @endif
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm_pr-6 whitespace-nowrap">
                        <span class="sr-only">Hành động</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($articles as $article)
                <tr>
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">{{ $article->id }}</td>
                    <td class="px-3 py-4 text-sm text-gray-500 max-w-[120px] sm_max-w-[180px] lg_max-w-xs truncate" title="{{ $article->title }}">
                        {{ $article->title }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $article->user?->name ?: '-' }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 hidden sm_table-cell">{{ \Carbon\Carbon::parse($article->created_at)->format('d/m/Y') }}</td>
                    @if(auth()->check() && auth()->user()->is_admin)
                    <td class="whitespace-nowrap px-3 py-4 text-center text-sm text-gray-500">
                        <span class="inline-flex items-center rounded-full bg-gray-50 border border-gray-200 px-2.5 py-0.5 text-xs font-semibold text-gray-600">
                            {{ number_format($article->views ?? 0) }}
                        </span>
                    </td>
                    @endif
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-center text-sm font-medium sm_pr-6">
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ rtrim(config('app.url'), '/') . route('articles.show', [$article->id], false) }}" class="text-indigo-600 hover_text-indigo-900" target="_blank" title="Xem">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>

                            <button type="button" onclick="copyListShareUrl(this, '{{ rtrim(config('app.url'), '/') . route('articles.show', ['idOrSlug' => $article->id], false) }}/?utm_source={{ $article->user?->username ?? 'admin' }}&utm_medium=social')" class="text-[#9d080a] hover_text-[#7a0608]" title="Copy Link Share">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                </svg>
                            </button>

                            <a href="{{ route('admin.edit', $article->id) }}" class="text-green-600 hover_text-green-900" title="Sửa">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                            </a>

                            <form action="{{ route('admin.delete', $article->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover_text-red-900 shrink-0" title="Xoá">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ (auth()->check() && auth()->user()->is_admin) ? '6' : '5' }}" class="py-4 text-center text-gray-500">Không có bài viết nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $articles->links() }}
    </div>
</div>
</div>

<script>
    function generateAi(btn) {
        if (btn.disabled) return;
        const originalText = btn.innerHTML;
        btn.innerHTML = '⏳ Đang tạo... (tối đa 1 phút)';
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');

        fetch(btn.dataset.url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json().then(data => ({
                status: res.status,
                body: data
            })))
            .then(res => {
                if (res.status === 200) {
                    alert(res.body.message || 'Khởi tạo bài viết thành công!');
                    window.location.reload();
                } else {
                    alert('Lỗi: ' + (res.body.error || 'Có lỗi xảy ra'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Lỗi: Không thể kết nối tới máy chủ AI.');
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            });
    }

    function copyListShareUrl(btn, url) {
        const textArea = document.createElement("textarea");
        textArea.value = url;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            const originalHTML = btn.innerHTML;
            // Hiển thị icon Check xanh
            btn.innerHTML = `<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;
            setTimeout(() => btn.innerHTML = originalHTML, 2000);
        } catch (err) {
            console.error('Lỗi copy', err);
        }
        document.body.removeChild(textArea);
    }
</script>
@endsection