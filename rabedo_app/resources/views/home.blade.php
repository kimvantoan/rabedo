@extends('layouts.app')

@php
$heroTitle = 'News & Stories: Drama Stories, Lifestyle Tales & Trending Topics';
$heroDesc = 'Explore drama stories, lifestyle moments, and trending topics shaping everyday life.';
@endphp

@section('title', $heroTitle)
@section('seo_meta')
<meta name="description" content="{{ $heroDesc }}">
<meta property="og:title" content="{{ $heroTitle }}">
<meta property="og:description" content="{{ $heroDesc }}">
<meta property="og:type" content="website">
<meta property="og:image" content="{{ asset('logo_sharp.png') }}">
@endsection

@section('content')
<!-- Hero Search Section -->
<div class="hero-section">
    <div class="hero-container-inner">
        <h1 class="hero-title-h1">
            {{ $heroTitle }}
        </h1>
        <p class="hero-desc-p">
            {{ $heroDesc }}
        </p>

        <form action="{{ route('home') }}" method="GET" class="hero-search-form">
            <div class="hero-search-icon-wrapper">
                <svg class="hero-search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ $heroDesc }}" class="hero-search-input" autocomplete="off">
            <button type="submit" class="hero-search-btn">
                Search
            </button>
        </form>

        <div class="ads-wrapper-top">
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
    </div>
</div>

<div class="home-container">
    <div class="section-header">
        <h2 class="section-title">
            {{ !empty($searchTerm) ? 'Search Results' : 'Latest Articles' }}
        </h2>
        @if(!empty($searchTerm))
        <p class="search-results-text">Found {{ $articles->total() }} results for "{{ $searchTerm }}"</p>
        @endif
    </div>

    @if($articles->count() > 0)
    <div class="article-grid">
        @foreach($articles as $article)
        <article class="article-card">
            @if($article->thumbnail)
            <div class="article-card-img-wrapper">
                <img src="{{ asset($article->thumbnail) }}" alt="{{ $article->title }}" class="object-cover w-full h-full" loading="lazy">
            </div>
            @endif
            <div class="article-card-content">
                <div class="article-card-date">
                    <time datetime="{{ $article->created_at }}">
                        {{ \Carbon\Carbon::parse($article->created_at)->format('M d, Y') }}
                    </time>
                </div>
                <div class="article-card-title-wrapper">
                    <h3 class="article-card-title">
                        <a href="{{ route('articles.show', [$article->id]) }}">
                            <span class="article-card-link-overlay"></span>
                            {{ $article->title }}
                        </a>
                    </h3>
                </div>
                <div class="article-card-desc">
                    {{ \Illuminate\Support\Str::limit(strip_tags($article->description ?: $article->content), 120) }}
                </div>
                <div class="article-card-footer">
                    <span class="article-card-readmore">
                        Read Article
                        <svg xmlns="http://www.w3.org/2000/svg" class="article-card-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </span>
                </div>
            </div>
        </article>
        @endforeach
    </div>
    <div class="pagination-wrapper pagination-theme">
        {{ $articles->links('vendor.pagination.home') }}
    </div>

    <style>
        /* Customizing the Tailwind Pagination to match search button color */
        .pagination-theme nav [aria-current="page"]>span {
            background-color: #681313 !important;
            color: white !important;
            border-color: #681313 !important;
            z-index: 10;
        }

        .pagination-theme nav a:hover,
        .pagination-theme nav a:focus {
            color: #681313 !important;
            border-color: #681313 !important;
        }
    </style>

    <!-- Ad Block - Bottom -->
    <div class="ads-wrapper-bottom">
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
    @else
    <p class="no-articles-text">No articles yet.</p>
    @endif
</div>
@endsection