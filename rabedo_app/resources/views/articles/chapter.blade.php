@extends('layouts.app')

@section('title', 'Chương ' . $chapter->chapter_number . ': ' . $chapter->title . ' | ' . $article->title)
@section('seo_meta')
    @php
        $plainTextDesc = Str::limit(strip_tags($chapter->content), 155);
    @endphp
    <meta name="description" content="{{ $plainTextDesc }}">
    <meta property="og:title" content="Chương {{ $chapter->chapter_number }}: {{ $chapter->title }} - {{ $article->title }}">
    <meta property="og:description" content="{{ $plainTextDesc }}">
    <meta property="og:type" content="article">
    <meta property="og:image" content="{{ (isset($article) && !empty($article->thumbnail)) ? asset($article->thumbnail) : asset('logo_sharp.png') }}">
@endsection

@section('content')
<article class="mx-auto max-w-4xl w-full px-4 sm:px-6 lg:px-8 mb-20 md:mb-24 bg-white pt-8 text-left">
    
    <!-- Breadcrumb -->
    <nav class="flex mb-8 text-sm text-gray-500 font-medium">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="inline-flex items-center hover:text-indigo-600 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    Trang chủ
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('articles.show', $article->slug ?: $article->id) }}" class="ml-1 hover:text-indigo-600 transition-colors md:ml-2">{{ $article->title }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-gray-400 md:ml-2">Chương {{ $chapter->chapter_number }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header Chương -->
    <div class="text-center mb-10 pb-6 border-b border-gray-100">
        <h1 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-[#1a1a1a] leading-[1.4] tracking-tight mb-4">
            Chương {{ $chapter->chapter_number }}: {{ $chapter->title }}
        </h1>
        <div class="flex items-center justify-center gap-2 text-sm text-gray-500 font-sans">
            <span class="font-bold uppercase text-gray-900 tracking-wide">{{ $article->title }}</span>
            <span>•</span>
            <span>Đăng lúc: {{ $chapter->created_at->format('H:i d/m/Y') }}</span>
        </div>
    </div>

    <div class="mt-2 mb-8 md:mb-10 w-full text-center">
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-4370452252708446"
             data-ad-slot="9674028583"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
        <script>
             (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
    
    <!-- Nội dung chính -->
    <div class="prose prose-lg md:prose-xl max-w-none w-full text-[#333]
        [&_h2]:text-2xl [&_h2]:md:text-[32px] [&_h2]:mt-12 [&_h2]:mb-6 [&_h2]:font-extrabold [&_h2]:text-gray-900 [&_h2]:tracking-tight [&_h2]:leading-tight
        [&_h3]:text-[22px] [&_h3]:md:text-[26px] [&_h3]:mt-10 [&_h3]:mb-4 [&_h3]:font-bold [&_h3]:text-gray-800 [&_h3]:leading-snug
        [&_h4]:text-[19px] [&_h4]:md:text-[22px] [&_h4]:mt-8 [&_h4]:mb-3 [&_h4]:font-bold [&_h4]:text-gray-700
        [&_p]:text-[18px] [&_p]:md:text-[20px] [&_p]:leading-[1.8] [&_p]:text-[#333] [&_p]:mb-6 [&_p:last-child]:mb-0 [&_p:empty]:hidden [&_p:has(>br:only-child)]:hidden [&_p]:font-sans [&_p]:whitespace-normal [&_p]:break-words [&_p]:text-left
        [&_blockquote]:border-l-4 [&_blockquote]:border-indigo-600 [&_blockquote]:pl-6 [&_blockquote]:md:pl-8 [&_blockquote]:italic [&_blockquote]:text-[22px] [&_blockquote]:md:text-[26px] [&_blockquote]:text-gray-900 [&_blockquote]:bg-gray-50 [&_blockquote]:py-6 [&_blockquote]:pr-6 [&_blockquote]:rounded-r-xl [&_blockquote]:my-10 [&_blockquote]:leading-relaxed [&_blockquote]:text-left
        [&_img]:rounded-xl [&_img]:w-full [&_img]:max-w-2xl [&_img]:mx-auto [&_img]:my-10 [&_img]:shadow-md
        [&_figcaption]:text-center [&_figcaption]:text-[13px] [&_figcaption]:text-gray-500 [&_figcaption]:mt-3
        [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:mb-8 [&_ul]:space-y-2 [&_li]:text-[18px] [&_li]:md:text-[20px] [&_li]:font-sans [&_li]:text-[#333] [&_li]:whitespace-normal [&_li]:break-words [&_li]:text-left">
        {!! $chapter->content !!}
    </div>

    <!-- Nút điều hướng -->
    <div class="mt-16 pt-8 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        @if($prevChapter)
            <a href="{{ route('articles.chapter', ['idOrSlug' => $article->slug ?: $article->id, 'chapterNumber' => $prevChapter->chapter_number]) }}" class="w-full sm:w-auto flex items-center justify-center px-6 py-3 bg-gray-100 hover:bg-indigo-600 hover:text-white rounded-lg text-gray-800 font-bold transition-colors group">
                <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Chương trước
            </a>
        @else
            <div class="w-full sm:w-auto px-6 py-3 text-center text-gray-400 font-medium">Bắt đầu truyện</div>
        @endif

        <a href="{{ route('articles.show', $article->slug ?: $article->id) }}" class="p-3 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-600 transition" title="Xem Mục lục">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
        </a>

        @if($nextChapter)
            <a href="{{ route('articles.chapter', ['idOrSlug' => $article->slug ?: $article->id, 'chapterNumber' => $nextChapter->chapter_number]) }}" class="w-full sm:w-auto flex items-center justify-center px-6 py-3 bg-gray-100 hover:bg-indigo-600 hover:text-white rounded-lg text-gray-800 font-bold transition-colors group">
                Chương sau
                <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        @else
            <div class="w-full sm:w-auto px-6 py-3 text-center text-gray-400 font-medium">Bạn đã đọc hết truyện</div>
        @endif
    </div>


</article>
@endsection
