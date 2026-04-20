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

    <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg w-full">
        <table class="min-w-full divide-y divide-gray-300 bg-white">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">ID</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tiêu đề</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Bút danh (Author)</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 hidden sm:table-cell">Ngày tạo</th>
                    <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Lượt xem</th>
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
                        <a href="{{ route('articles.show', $article->id) }}" target="_blank">{{ $article->title }}</a>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $article->author ?: 'Admin' }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 hidden sm:table-cell">{{ \Carbon\Carbon::parse($article->created_at)->format('d/m/Y') }}</td>
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
                    <td colspan="6" class="py-4 text-center text-gray-500">Người dùng này chưa có bài đăng nào</td>
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
