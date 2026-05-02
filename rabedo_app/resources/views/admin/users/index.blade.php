@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm_px-6 lg_px-8">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Admin Dashboard</h1>
        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover_bg-gray-50 transition">
                Đăng xuất
            </button>
        </form>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 mb-8">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="{{ route('admin.dashboard') }}" class="border-transparent text-gray-500 hover_text-gray-700 hover_border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Quản lý Bài viết
            </a>
            <a href="{{ route('users.index') }}" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" aria-current="page">
                Quản lý Tài khoản
            </a>
        </nav>
    </div>

    <div class="flex justify-end mb-4">
        <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover_bg-indigo-700 transition">
            + Thêm tài khoản mới
        </a>
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

    <div class="mb-4 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('users.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="relative w-full sm_w-auto">
                <input type="date" name="view_date" id="view_date" value="{{ request('view_date') }}" onchange="document.getElementById('view_month').value=''" title="Lọc theo ngày cụ thể" class="block w-full px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white text-gray-700 focus_outline-none focus_ring-1 focus_ring-indigo-500 focus_border-indigo-500 sm_text-sm">
            </div>
            <div class="relative w-full sm_w-auto">
                <input type="month" name="view_month" id="view_month" value="{{ request('view_month') }}" onchange="document.getElementById('view_date').value=''" title="Lọc theo tháng" class="block w-full px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white text-gray-700 focus_outline-none focus_ring-1 focus_ring-indigo-500 focus_border-indigo-500 sm_text-sm">
            </div>
            <div class="flex items-center gap-2 w-full sm_w-auto">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover_bg-indigo-700 w-full sm_w-auto">Lọc View</button>
                @if(request('view_date') || request('view_month'))
                <a href="{{ route('users.index') }}" class="inline-flex justify-center py-2 px-4 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white border border-gray-300 hover_bg-gray-50 whitespace-nowrap w-full sm_w-auto text-center">Xoá lọc</a>
                @endif
            </div>
        </form>
    </div>

    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md_rounded-lg">
        <table class="min-w-full divide-y divide-gray-300 bg-white">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">ID</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tên</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Username</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Ngày tạo</th>
                    <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Tổng View</th>
                    <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Số bài đăng</th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm_pr-6">
                        <span class="sr-only">Hành động</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($users as $user)
                <tr>
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">{{ $user->id }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900">{{ $user->name }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $user->username }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-center text-sm text-gray-500">
                        <span class="inline-flex items-center rounded-full bg-purple-50 border border-purple-200 px-2.5 py-0.5 text-xs font-semibold text-purple-600">
                            {{ number_format($user->total_views ?? 0) }}
                        </span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-center text-sm text-gray-500">
                        <span class="inline-flex items-center rounded-full bg-blue-50 border border-blue-200 px-2.5 py-0.5 text-xs font-semibold text-blue-600">
                            {{ $user->articles_count }}
                        </span>
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-center text-sm font-medium sm_pr-6">
                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('users.show', $user->id) }}" class="text-indigo-600 hover_text-indigo-900" title="Xem">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                            
                            <a href="{{ route('users.edit', $user->id) }}" class="text-green-600 hover_text-green-900" title="Sửa">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </a>
                            
                            @if(auth()->id() !== $user->id)
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Khi xoá tài khoản này, các bài viết của họ sẽ không bị mất (trở thành tác giả trống). Bạn có chắc chắn muốn xóa tài khoản này không?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover_text-red-900 shrink-0" title="Xoá">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            @else
                                <!-- Space reservation to keep align -->
                                <div class="w-5 h-5 inline-block"></div>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-4 text-center text-gray-500">Chưa có hệ thống tài khoản nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
