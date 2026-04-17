@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Admin Dashboard</h1>
        <div class="flex space-x-3">
            <button onclick="generateAi(this)" data-url="{{ route('admin.generate_ai') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-purple-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-purple-700 transition">
                ✨ Tự động tạo bài AI
            </button>
            <a href="{{ route('admin.editor') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition">
                + Viết bài mới
            </a>
        </div>
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

    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button onclick="switchTab('articles-tab', this)" class="tab-btn whitespace-nowrap border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium text-indigo-600" aria-current="page">
                Danh sách bài đăng Admin
            </button>
            <button onclick="switchTab('stats-tab', this)" class="tab-btn whitespace-nowrap border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                Thống kê Tác giả
            </button>
        </nav>
    </div>

    <!-- Tab Articles -->
    <div id="articles-tab" class="tab-content block">
        <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-4 shadow ring-1 ring-black ring-opacity-5 rounded-lg">
            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex w-full max-w-lg items-center">
                @if(request('sort_views'))
                    <input type="hidden" name="sort_views" value="{{ request('sort_views') }}">
                @endif
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm theo tiêu đề hoặc tác giả..." class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 mr-3">
                <button type="submit" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 whitespace-nowrap">Tìm kiếm</button>
            </form>
            @if(request('q') || request('sort_views'))
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700 underline whitespace-nowrap">Xóa bộ lọc</a>
            @endif
        </div>
        
        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300 bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">ID</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tiêu đề</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tác giả</th>
                        {{-- <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Phân loại</th> --}}
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Ngày tạo</th>
                        <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">
                            @php
                                $nextSort = (request('sort_views') == 'desc') ? 'asc' : 'desc';
                            @endphp
                            <a href="{{ request()->fullUrlWithQuery(['sort_views' => $nextSort]) }}" class="group inline-flex items-center hover:text-indigo-600">
                                Lượt xem
                                <span class="ml-2 flex-none rounded text-gray-400 group-hover:text-indigo-600">
                                    @if(request('sort_views') == 'asc')
                                        &#9650;
                                    @else
                                        &#9660;
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                            <span class="sr-only">Hành động</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($articles as $article)
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">{{ $article->id }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $article->title }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 font-medium">{{ $article->author ?: 'Admin (Chưa rõ)' }}</td>
                        {{-- <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            <span class="inline-flex rounded-full bg-blue-100 px-2 text-xs font-semibold leading-5 text-blue-800">{{ $article->type }}</span>
                        </td> --}}
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($article->created_at)->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-center text-sm text-gray-500">
                            <span class="inline-flex items-center rounded-full bg-gray-50 border border-gray-200 px-2.5 py-0.5 text-xs font-semibold text-gray-600">
                                {{ number_format($article->views ?? 0) }}
                            </span>
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                            <a href="{{ route('articles.show', $article->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3" target="_blank">Xem</a>
                            <a href="{{ route('admin.edit', $article->id) }}" class="text-green-600 hover:text-green-900 mr-3">Sửa</a>
                            <form action="{{ route('admin.delete', $article->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Xoá</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-4 text-center text-gray-500">Không có bài viết nào</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $articles->links() }}
        </div>
    </div>

    <!-- Tab Stats -->
    <div id="stats-tab" class="tab-content hidden">
        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300 bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Tên Tác giả</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Số lượng bài đăng</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($authorStats as $stat)
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">{{ $stat->author ?: 'Admin (Chưa rõ)' }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                {{ number_format($stat->total) }} bài viết
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="py-4 text-center text-gray-500">Chưa có dữ liệu tác giả</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $authorStats->appends(request()->query())->links() }}
        </div>
    </div>
</div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('stats_page')) {
        const statsBtn = document.querySelector('button[onclick="switchTab(\'stats-tab\', this)"]');
        if (statsBtn) switchTab('stats-tab', statsBtn);
    }
});

function switchTab(tabId, btn) {
    // Hide all tab content
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.add('hidden');
        el.classList.remove('block');
    });
    
    // Show selected tab content
    document.getElementById(tabId).classList.remove('hidden');
    document.getElementById(tabId).classList.add('block');
    
    // Reset all tabs styles
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('border-indigo-500', 'text-indigo-600');
        el.classList.add('border-transparent', 'text-gray-500', 'hover:border-gray-300', 'hover:text-gray-700');
    });
    
    // Set active tab style
    btn.classList.remove('border-transparent', 'text-gray-500', 'hover:border-gray-300', 'hover:text-gray-700');
    btn.classList.add('border-indigo-500', 'text-indigo-600');
}

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
    .then(res => res.json().then(data => ({status: res.status, body: data})))
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
</script>
@endsection
