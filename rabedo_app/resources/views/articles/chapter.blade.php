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
<article class="article-container">
    
    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" class="breadcrumb-link">
                    <svg class="chapter-breadcrumb-icon" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    Trang chủ
                </a>
            </li>
            <li>
                <div class="breadcrumb-inner">
                    <svg class="chapter-breadcrumb-icon-gray" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('articles.show', $article->slug ?: $article->id) }}" class="breadcrumb-link-inner">{{ $article->title }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="breadcrumb-inner">
                    <svg class="chapter-breadcrumb-icon-gray" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="breadcrumb-text-inner">Chương {{ $chapter->chapter_number }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header Chương -->
    <div class="chapter-header-wrapper">
        <h1 class="chapter-header-title">
            Chương {{ $chapter->chapter_number }}: {{ $chapter->title }}
        </h1>
        <div class="chapter-meta-wrapper">
            <span class="chapter-meta-title">{{ $article->title }}</span>
            <span>•</span>
            <span>Đăng lúc: {{ $chapter->created_at->format('H:i d/m/Y') }}</span>
        </div>
    </div>

    <div class="ads-wrapper-top-chapter">
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
    <div class="rabedo-prose-chapter">
        {!! $chapter->content !!}
    </div>

    <!-- Nút điều hướng -->
    <div class="chapter-bottom-nav">
        @if($prevChapter)
            <a href="{{ route('articles.chapter', ['idOrSlug' => $article->slug ?: $article->id, 'chapterNumber' => $prevChapter->chapter_number]) }}" class="chapter-nav-btn-alt">
                <svg class="chapter-nav-icon-prev" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Chương trước
            </a>
        @else
            <div class="chapter-nav-btn-alt-disabled">Bắt đầu truyện</div>
        @endif

        <a href="{{ route('articles.show', $article->slug ?: $article->id) }}" class="chapter-nav-btn-index" title="Xem Mục lục">
            <svg class="chapter-nav-btn-index-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
        </a>

        @if($nextChapter)
            <a href="{{ route('articles.chapter', ['idOrSlug' => $article->slug ?: $article->id, 'chapterNumber' => $nextChapter->chapter_number]) }}" class="chapter-nav-btn-alt">
                Chương sau
                <svg class="chapter-nav-icon-next" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        @else
            <div class="chapter-nav-btn-alt-disabled">Bạn đã đọc hết truyện</div>
        @endif
    </div>


</article>
@endsection
