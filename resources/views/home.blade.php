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
            <div class="text-center text-xs text-gray-400 mt-2" style="font-size: 11px; color: #9ca3af; letter-spacing: 0.05em; text-transform: uppercase;">Advertisements</div>
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
        <x-article-card :article="$article" />
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
        <div class="text-center text-xs text-gray-400 mt-2" style="font-size: 11px; color: #9ca3af; letter-spacing: 0.05em; text-transform: uppercase;">Advertisements</div>
    </div>
    @else
    <p class="no-articles-text">No articles yet.</p>
    @endif
</div>
@endsection