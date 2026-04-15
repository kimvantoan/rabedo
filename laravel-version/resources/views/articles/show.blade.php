@extends('layouts.app')

@section('title', $article->title . ' | RabedoNews')
@section('seo_meta')
    @php
        $plainTextDesc = Str::limit(strip_tags($article->content), 155);
    @endphp
    <meta name="description" content="{{ $plainTextDesc }}">
    <meta property="og:title" content="{{ $article->title }} | RabedoNews">
    <meta property="og:description" content="{{ $plainTextDesc }}">
    <meta property="og:type" content="article">
    <meta property="og:image" content="{{ (isset($article) && !empty($article->thumbnail)) ? asset($article->thumbnail) : asset('logo.png') }}">
    <meta property="article:published_time" content="{{ $article->created_at }}">
    <meta property="article:author" content="{{ $article->author ?: 'Quản trị viên' }}">
@endsection

@section('content')
<div class="container mx-auto px-4 lg:px-6 mb-20 md:mb-24 bg-white pt-12">
    <div class="max-w-[800px] mx-auto w-full text-left">
        <h1 class="text-[32px] md:text-[40px] lg:text-[44px] font-extrabold text-[#1a1a1a] leading-[1.3] tracking-tight mb-5">
            {{ $article->title }}
        </h1>
        <div class="flex items-center gap-1.5 text-[14px] text-gray-400 font-sans flex-wrap mb-10 md:mb-12">
            <span class="uppercase text-gray-400 text-[12px] font-medium">Bởi</span>
            <span class="font-bold text-gray-900 uppercase mr-1 text-[13px] tracking-wide">{{ $article->author ?: 'Quản trị viên' }}</span>
            <span class="text-gray-300">—</span>
            <span class="ml-1">{{ \Carbon\Carbon::parse($article->created_at)->day }} tháng {{ \Carbon\Carbon::parse($article->created_at)->month }}, {{ \Carbon\Carbon::parse($article->created_at)->year }}</span>
            <span class="mx-1">trong</span>
            <span class="text-gray-400 font-medium">{{ $article->type ?: 'Tin tức' }}</span>
        </div>
        
        <div class="prose prose-lg md:prose-xl max-w-none w-full text-[#333]
            [&_h2]:text-2xl [&_h2]:md:text-[32px] [&_h2]:mt-12 [&_h2]:mb-6 [&_h2]:font-extrabold [&_h2]:text-gray-900 [&_h2]:tracking-tight [&_h2]:leading-tight
            [&_h3]:text-[22px] [&_h3]:md:text-[26px] [&_h3]:mt-10 [&_h3]:mb-4 [&_h3]:font-bold [&_h3]:text-gray-800 [&_h3]:leading-snug
            [&_h4]:text-[19px] [&_h4]:md:text-[22px] [&_h4]:mt-8 [&_h4]:mb-3 [&_h4]:font-bold [&_h4]:text-gray-700
            [&_p]:text-[18px] [&_p]:md:text-[20px] [&_p]:leading-[1.65] [&_p]:text-[#333] [&_p]:mb-7 [&_p]:font-sans [&_p]:whitespace-normal [&_p]:break-words [&_p]:text-justify
            [&_blockquote]:border-l-4 [&_blockquote]:border-red-600 [&_blockquote]:pl-6 [&_blockquote]:md:pl-8 [&_blockquote]:italic [&_blockquote]:text-[22px] [&_blockquote]:md:text-[26px] [&_blockquote]:text-gray-900 [&_blockquote]:bg-gray-50 [&_blockquote]:py-6 [&_blockquote]:pr-6 [&_blockquote]:rounded-r-xl [&_blockquote]:my-10 [&_blockquote]:leading-relaxed [&_blockquote]:text-justify
            [&_img]:rounded-none [&_img]:w-full [&_img]:my-10 [&_img]:shadow-md
            [&_figcaption]:text-center [&_figcaption]:text-[13px] [&_figcaption]:text-gray-500 [&_figcaption]:mt-3
            [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:mb-8 [&_ul]:space-y-2 [&_li]:text-[18px] [&_li]:md:text-[20px] [&_li]:font-sans [&_li]:text-[#333] [&_li]:whitespace-normal [&_li]:break-words [&_li]:text-justify">
            {!! $article->content !!}
        </div>
    </div>
</div>
@endsection
