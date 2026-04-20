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
<div class="py-20 px-4 sm:px-6 lg:px-8 w-full border-b border-gray-100 bg-white">
    <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-[54px] font-extrabold tracking-tight mb-6 leading-tight md:leading-[1.15] max-w-4xl mx-auto" style="color: #9d080a;">
            {{ $heroTitle }}
        </h1>
        <p class="text-lg sm:text-xl md:text-[22px] text-gray-500 mb-10 font-medium leading-relaxed max-w-3xl mx-auto">
            {{ $heroDesc }}
        </p>
        
        <form action="{{ route('home') }}" method="GET" class="relative max-w-3xl mx-auto flex items-center bg-white rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-gray-200 overflow-hidden p-2 transition-all hover:shadow-[0_8px_30px_rgb(0,0,0,0.12)]">
            <div class="flex-shrink-0 pl-4 pr-1">
                <svg class="h-6 w-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ $heroDesc }}" class="block w-full border-0 bg-transparent py-3 px-3 text-gray-900 placeholder-gray-400 focus:ring-0 sm:text-lg focus:outline-none" autocomplete="off">
            <button type="submit" class="flex-shrink-0 flex items-center justify-center font-semibold text-white transition-colors focus:outline-none rounded-full ml-2 w-auto min-w-[120px] h-12" style="background-color: #681313;">
                Search
            </button>
        </form>
    </div>
</div>
<!-- End Hero Search Section -->

<!-- Ad Block - Top -->
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 mt-8">
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

<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="border-b-4 border-gray-900 mb-8 pb-3">
        <h2 class="text-3xl font-serif font-bold text-gray-900 uppercase tracking-widest">
            {{ !empty($searchTerm) ? 'Search Results' : 'Latest Articles' }}
        </h2>
        @if(!empty($searchTerm))
            <p class="text-gray-500 mt-2">Found {{ $articles->total() }} results for "{{ $searchTerm }}"</p>
        @endif
    </div>
    
    @if($articles->count() > 0)
        <div class="grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($articles as $article)
            <article class="group relative flex flex-col bg-white border-0 transition-colors cursor-pointer">
                @if($article->thumbnail)
                    <div class="relative w-full h-48 sm:h-56 mb-4 rounded-2xl overflow-hidden shrink-0">
                        <img src="{{ asset($article->thumbnail) }}" alt="{{ $article->title }}" class="object-cover w-full h-full" loading="lazy">
                    </div>
                @endif
                <div class="flex flex-col flex-1 px-1">
                    <div class="text-[0.9rem] text-gray-500 mb-2 font-medium">
                        <time datetime="{{ $article->created_at }}">
                            {{ \Carbon\Carbon::parse($article->created_at)->format('M d, Y') }}
                        </time>
                    </div>
                    <div class="mb-3">
                        <h3 class="text-[1.25rem] font-bold font-sans leading-tight text-[#0a1e3f] group-hover:text-[#9d080a] transition-colors line-clamp-2">
                            <a href="{{ route('articles.show', [$article->id, 'utm_source' => $article->user?->username]) }}">
                                <span class="absolute inset-0"></span>
                                {{ $article->title }}
                            </a>
                        </h3>
                    </div>
                    <div class="text-gray-600 text-[0.95rem] line-clamp-3 mb-4 leading-relaxed">
                        {{ \Illuminate\Support\Str::limit(strip_tags($article->content), 120) }}
                    </div>
                    <div class="mt-auto flex justify-end">
                        <span class="text-[#9d080a] font-bold text-sm flex items-center gap-1 group-hover:text-[#681313] transition-colors relative">
                            Read Article
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 stroke-[2.5px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </span>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
        <div class="mt-8 pagination-theme flex justify-center w-full">
            {{ $articles->links('vendor.pagination.home') }}
        </div>

        <style>
            /* Customizing the Tailwind Pagination to match search button color */
            .pagination-theme nav [aria-current="page"] > span {
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
        <div class="mt-12 mb-4 w-full">
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
        <p class="text-gray-500">No articles yet.</p>
    @endif
</div>
@endsection
