@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Chi Tiết Tài Khoản</h1>
        <div class="flex space-x-3">
            <a href="{{ route('users.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 px-4 py-2">
                &larr; Quay lại
            </a>
            <a href="{{ route('users.edit', $user->id) }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition">
                Sửa thông tin
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- User Information (Left Column) -->
        <div class="lg:col-span-1">
            <div class="overflow-hidden bg-white shadow sm:rounded-lg mb-8">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Thông tin tài khoản</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-4 px-4 sm:px-6">
                            <dt class="text-xs font-medium text-gray-500 uppercase">Tên đầy đủ</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $user->name }}</dd>
                        </div>
                        <div class="py-4 px-4 sm:px-6">
                            <dt class="text-xs font-medium text-gray-500 uppercase">Username</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-700">{{ $user->username }}</dd>
                        </div>
                        <div class="py-4 px-4 sm:px-6">
                            <dt class="text-xs font-medium text-gray-500 uppercase">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900 break-all">{{ $user->email }}</dd>
                        </div>
                        <div class="py-4 px-4 sm:px-6">
                            <dt class="text-xs font-medium text-gray-500 uppercase">Ngày tham gia</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($user->created_at)->format('H:i d/m/Y') }}</dd>
                        </div>
                        <div class="py-4 px-4 sm:px-6">
                            <dt class="text-xs font-medium text-gray-500 uppercase">Tổng số bài đã đăng</dt>
                            <dd class="mt-1 text-sm text-gray-900 border rounded px-2 py-1 bg-gray-50 inline-block font-semibold">
                                {{ $user->articles_count }} bài viết
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- User's Articles List (Right Column) -->
        <div class="lg:col-span-3">
            <div class="mb-4">
                <h2 class="text-xl font-bold tracking-tight text-gray-900">Danh sách bài đăng của {{ $user->name }}</h2>
            </div>

            <div class="mb-4 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <form action="{{ route('users.show', $user->id) }}" method="GET" class="flex flex-wrap sm:flex-nowrap items-center gap-3">
                    <div class="relative flex-grow max-w-md w-full sm:w-auto">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm tiêu đề bài viết..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div class="relative flex-grow max-w-[200px] w-full sm:w-auto">
                        <input type="date" name="date" value="{{ request('date') }}" title="Lọc theo ngày đăng bài" class="block w-full px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div class="flex items-center gap-2 mt-2 sm:mt-0 w-full sm:w-auto">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 w-full sm:w-auto">Tìm kiếm</button>
                        @if(request('search') || request('date'))
                        <a href="{{ route('users.show', $user->id) }}" class="inline-flex justify-center py-2 px-4 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 whitespace-nowrap w-full sm:w-auto text-center">Xoá lọc</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg w-full">
                <table class="min-w-full divide-y divide-gray-300 bg-white">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">ID</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tiêu đề</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 hidden sm:table-cell">Ngày tạo</th>
                            <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">
                                @php
                                $viewSortDir = request('sort') === 'views' && request('dir') === 'desc' ? 'asc' : 'desc';
                                @endphp
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'views', 'dir' => $viewSortDir]) }}" class="group inline-flex items-center hover:text-indigo-600">
                                    Lượt xem
                                    <span class="ml-1 flex-none rounded bg-gray-100 text-gray-900 group-hover:bg-gray-200">
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
                                        <svg class="h-4 w-4 text-gray-400 invisible group-hover:visible" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                        @endif
                                    </span>
                                </a>
                            </th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 whitespace-nowrap">
                                <span class="sr-only">Hành động</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($articles as $article)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">{{ $article->id }}</td>
                            <td class="px-3 py-4 text-sm font-medium text-indigo-600 hover:text-indigo-900 max-w-[120px] sm:max-w-[180px] lg:max-w-[200px] truncate" title="{{ $article->title }}">
                                <a href="{{ route('articles.show', [$article->id, 'utm_source' => $article->user?->username]) }}" target="_blank">{{ $article->title }}</a>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 hidden sm:table-cell">{{ \Carbon\Carbon::parse($article->created_at)->format('d/m/Y') }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-center text-sm text-gray-500">
                                <span class="inline-flex items-center rounded-full bg-gray-50 border border-gray-200 px-2.5 py-0.5 text-xs font-semibold text-gray-600">
                                    {{ number_format($article->views ?? 0) }}
                                </span>
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-center text-sm font-medium sm:pr-6">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('articles.show', [$article->id]) }}" class="text-indigo-600 hover:text-indigo-900" target="_blank" title="Xem">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>

                                    <button type="button" onclick="copyListShareUrl(this, '{{ route('articles.show', ['idOrSlug' => $article->id]) }}/?utm_source={{ $article->user?->username ?? 'admin' }}&utm_medium=social')" class="text-[#9d080a] hover:text-[#7a0608]" title="Copy Link Share">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                        </svg>
                                    </button>

                                    <a href="{{ route('admin.edit', $article->id) }}" class="text-green-600 hover:text-green-900" title="Sửa">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </a>

                                    <form action="{{ route('admin.delete', $article->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 shrink-0" title="Xoá">
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
                            <td colspan="5" class="py-4 text-center text-gray-500">Người dùng này chưa có bài đăng nào</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $articles->links() }}
            </div>
        </div> <!-- End Right Column -->
    </div> <!-- End Grid -->
</div>
@endsection

<script>
    function copyListShareUrl(btn, url) {
        const textArea = document.createElement("textarea");
        textArea.value = url;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = `<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;
            setTimeout(() => btn.innerHTML = originalHTML, 2000);
        } catch (err) {}
        document.body.removeChild(textArea);
    }
</script>