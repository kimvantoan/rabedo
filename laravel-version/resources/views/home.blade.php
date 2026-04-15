@extends('layouts.app')

@section('title', 'Trang chủ - Tin tức nổi bật cập nhật liên tục | RabedoNews')
@section('seo_meta')
    <meta name="description" content="RabedoNews cập nhật tin tức, sự kiện, xu hướng đời sống nóng hổi và chính xác nhất hàng ngày.">
    <meta property="og:title" content="Trang chủ - Tin tức nổi bật cập nhật liên tục | RabedoNews">
    <meta property="og:description" content="RabedoNews cung cấp tin tức nóng hổi, phân tích chuyên sâu đa chiều 24/7.">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('logo.png') }}">
@endsection

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900 mb-8 mt-12 bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">Bài viết mới nhất</h1>
    
    @if($articles->count() > 0)
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($articles as $article)
            <article class="group relative flex flex-col items-start justify-between bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 transition-all hover:shadow-md hover:-translate-y-1 cursor-pointer">
                @if($article->thumbnail)
                    <div class="relative w-full aspect-[16/9] mb-4 bg-gray-100 rounded-xl overflow-hidden">
                        <img src="{{ asset($article->thumbnail) }}" alt="{{ $article->title }}" class="object-cover w-full h-full" loading="lazy">
                    </div>
                @endif
                <div class="flex items-center gap-x-4 text-xs">
                    <time datetime="{{ $article->created_at }}" class="text-gray-500">
                        {{ \Carbon\Carbon::parse($article->created_at)->format('d/m/Y') }}
                    </time>
                    <span class="relative z-10 rounded-full bg-gray-50 px-3 py-1.5 font-medium text-gray-600 hover:bg-gray-100">
                        {{ $article->type }}
                    </span>
                </div>
                <div class="mt-3">
                    <h3 class="text-lg font-semibold leading-6 text-gray-900 group-hover:text-blue-600 line-clamp-2">
                        <a href="{{ route('articles.show', $article->id) }}">
                            <span class="absolute inset-0"></span>
                            {{ $article->title }}
                        </a>
                    </h3>
                </div>
            </article>
            @endforeach
        </div>
        <div class="mt-8">
            {{ $articles->links() }}
        </div>
    @else
        <p class="text-gray-500">Chưa có bài viết nào.</p>
    @endif
</div>
@endsection
