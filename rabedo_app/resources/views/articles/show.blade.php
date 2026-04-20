@extends('layouts.app')

@section('title', $article->title . ' | RabedoNews')
@section('seo_meta')
    @php
        $plainTextDesc = $article->description ?: Str::limit(strip_tags($article->content), 155);
    @endphp
    <meta name="description" content="{{ $plainTextDesc }}">
    <meta property="og:title" content="{{ $article->title }} | RabedoNews">
    <meta property="og:description" content="{{ $plainTextDesc }}">
    <meta property="og:type" content="article">
    <meta property="og:image" content="{{ (isset($article) && !empty($article->thumbnail)) ? asset($article->thumbnail) : asset('logo_sharp.png') }}">
    <meta property="article:published_time" content="{{ $article->created_at }}">
    <meta property="article:author" content="{{ $article->author ?: 'Quản trị viên' }}">
@endsection

@section('content')
<article class="mx-auto max-w-4xl w-full px-4 sm:px-6 lg:px-8 mb-20 md:mb-24 bg-white pt-12 text-left">
    <h1 class="text-[32px] md:text-[40px] lg:text-[44px] font-extrabold text-[#1a1a1a] leading-[1.3] tracking-tight mb-5">
            {{ $article->title }}
        </h1>
        <div class="mt-2 mb-5 md:mb-6 w-full text-center">
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
        
        @if(!empty($article->description))
        <p class="text-xl md:text-[22px] text-gray-600 leading-relaxed font-sans font-medium mb-6">
            {{ $article->description }}
        </p>
        @endif
        
        <div class="flex items-center justify-center gap-1.5 text-[14px] text-gray-400 font-sans flex-wrap mt-8 mb-10 md:mb-12">
            <span class="uppercase text-gray-400 text-[12px] font-medium">By</span>
            <span class="font-bold text-gray-900 uppercase mr-1 text-[13px] tracking-wide">{{ $article->author ?: 'Admin' }}</span>
            <span class="text-gray-300">—</span>
            <span class="ml-1">{{ \Carbon\Carbon::parse($article->created_at)->format('M d, Y') }}</span>
        </div>

        @if(!empty($article->thumbnail))
        <div class="w-full mb-10 md:mb-12">
            <img src="{{ asset($article->thumbnail) }}" alt="{{ $article->title }}" class="w-full h-auto object-cover rounded-xl shadow-sm md:max-h-[500px]" loading="eager">
        </div>
        @endif
        
        <div class="prose prose-lg md:prose-xl max-w-none w-full text-[#333]
            [&_h2]:text-2xl [&_h2]:md:text-[32px] [&_h2]:mt-12 [&_h2]:mb-6 [&_h2]:font-extrabold [&_h2]:text-gray-900 [&_h2]:tracking-tight [&_h2]:leading-tight
            [&_h3]:text-[22px] [&_h3]:md:text-[26px] [&_h3]:mt-10 [&_h3]:mb-4 [&_h3]:font-bold [&_h3]:text-gray-800 [&_h3]:leading-snug
            [&_h4]:text-[19px] [&_h4]:md:text-[22px] [&_h4]:mt-8 [&_h4]:mb-3 [&_h4]:font-bold [&_h4]:text-gray-700
            [&_p]:text-[18px] [&_p]:md:text-[20px] [&_p]:leading-[1.65] [&_p]:text-[#333] [&_p]:mb-6 [&_p:last-child]:mb-0 [&_p:empty]:hidden [&_p:has(>br:only-child)]:hidden [&_p]:font-sans [&_p]:whitespace-normal [&_p]:break-words [&_p]:text-left
            [&_blockquote]:border-l-4 [&_blockquote]:border-red-600 [&_blockquote]:pl-6 [&_blockquote]:md:pl-8 [&_blockquote]:italic [&_blockquote]:text-[22px] [&_blockquote]:md:text-[26px] [&_blockquote]:text-gray-900 [&_blockquote]:bg-gray-50 [&_blockquote]:py-6 [&_blockquote]:pr-6 [&_blockquote]:rounded-r-xl [&_blockquote]:my-10 [&_blockquote]:leading-relaxed [&_blockquote]:text-left
            [&_img]:rounded-none [&_img]:w-full [&_img]:my-10 [&_img]:shadow-md
            [&_figcaption]:text-center [&_figcaption]:text-[13px] [&_figcaption]:text-gray-500 [&_figcaption]:mt-3
            [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:mb-8 [&_ul]:space-y-2 [&_li]:text-[18px] [&_li]:md:text-[20px] [&_li]:font-sans [&_li]:text-[#333] [&_li]:whitespace-normal [&_li]:break-words [&_li]:text-left">
            {!! $article->content !!}
        </div>

        <div class="mt-4 md:mt-6 w-full text-center">
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
</article>

@if(isset($relatedArticles) && $relatedArticles->count() > 0)
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 mb-20">
    <div class="border-b-4 border-gray-900 mb-8 pb-3">
        <h2 class="text-3xl font-serif font-bold text-gray-900 uppercase tracking-widest">
            Related Articles
        </h2>
    </div>
    
    <div class="grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($relatedArticles as $relArticle)
        <article class="group relative flex flex-col bg-white border-0 transition-colors cursor-pointer">
            @if($relArticle->thumbnail)
                <div class="relative w-full h-48 sm:h-56 mb-4 rounded-2xl overflow-hidden shrink-0">
                    <img src="{{ asset($relArticle->thumbnail) }}" alt="{{ $relArticle->title }}" class="object-cover w-full h-full" loading="lazy">
                </div>
            @endif
            <div class="flex flex-col flex-1 px-1">
                <div class="text-[0.9rem] text-gray-500 mb-2 font-medium">
                    <time datetime="{{ $relArticle->created_at }}">
                        {{ \Carbon\Carbon::parse($relArticle->created_at)->format('M d, Y') }}
                    </time>
                </div>
                <div class="mb-3">
                    <h3 class="text-[1.25rem] font-bold font-sans leading-tight text-[#0a1e3f] group-hover:text-[#9d080a] transition-colors line-clamp-2">
                        <a href="{{ route('articles.show', [$relArticle->id, 'utm_source' => $relArticle->user?->username]) }}">
                            <span class="absolute inset-0"></span>
                            {{ $relArticle->title }}
                        </a>
                    </h3>
                </div>
                <div class="text-gray-600 text-[0.95rem] line-clamp-3 mb-4 leading-relaxed">
                    {{ \Illuminate\Support\Str::limit(strip_tags($relArticle->content), 120) }}
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
</div>
@endif
@endsection
