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

    @if(!(isset($currentChapter) && $currentChapter->chapter_number >= 2))
    @if(!empty($article->description))
    <p class="text-xl md:text-[22px] text-gray-600 leading-relaxed font-sans font-medium mb-6">
        {{ $article->description }}
    </p>
    @endif
    @endif

    @if(!(isset($currentChapter) && $currentChapter->chapter_number >= 2))
    <div class="flex items-center justify-center gap-1.5 text-[14px] text-gray-400 font-sans flex-wrap mt-8 mb-10 md:mb-12">
        <span class="uppercase text-gray-400 text-[12px] font-medium">By</span>
        <span class="font-bold text-gray-900 uppercase mr-1 text-[13px] tracking-wide">{{ $article->author ?: 'Admin' }}</span>
        <span class="text-gray-300">—</span>
        <span class="ml-1">{{ \Carbon\Carbon::parse($article->created_at)->format('M d, Y') }}</span>
    </div>
    @endif

    @if(!(isset($currentChapter) && $currentChapter->chapter_number >= 2))
    @if(!empty($article->thumbnail))
    <div class="w-full mb-10 md:mb-12">
        <img src="{{ asset($article->thumbnail) }}" alt="{{ $article->title }}" class="w-full h-auto object-cover rounded-xl shadow-sm md:max-h-[500px]" loading="eager">
    </div>
    @endif
    @endif

    <div id="article-content-wrapper" class="prose prose-lg md:prose-xl max-w-none w-full text-[#333]
            [&_h2]:text-2xl [&_h2]:md:text-[32px] [&_h2]:mt-12 [&_h2]:mb-6 [&_h2]:font-extrabold [&_h2]:text-gray-900 [&_h2]:tracking-tight [&_h2]:leading-tight
            [&_h3]:text-[22px] [&_h3]:md:text-[26px] [&_h3]:mt-10 [&_h3]:mb-4 [&_h3]:font-bold [&_h3]:text-gray-800 [&_h3]:leading-snug
            [&_h4]:text-[19px] [&_h4]:md:text-[22px] [&_h4]:mt-8 [&_h4]:mb-3 [&_h4]:font-bold [&_h4]:text-gray-700
            [&_p]:text-[18px] [&_p]:md:text-[20px] [&_p]:leading-[1.65] [&_p]:text-[#333] [&_p]:mb-6 [&_p:last-child]:mb-0 [&_p:empty]:hidden [&_p:has(>br:only-child)]:hidden [&_p]:font-sans [&_p]:whitespace-normal [&_p]:break-words [&_p]:text-left
            [&_blockquote]:border-l-4 [&_blockquote]:border-red-600 [&_blockquote]:pl-6 [&_blockquote]:md:pl-8 [&_blockquote]:italic [&_blockquote]:text-[22px] [&_blockquote]:md:text-[26px] [&_blockquote]:text-gray-900 [&_blockquote]:bg-gray-50 [&_blockquote]:py-6 [&_blockquote]:pr-6 [&_blockquote]:rounded-r-xl [&_blockquote]:my-10 [&_blockquote]:leading-relaxed [&_blockquote]:text-left
            [&_img]:rounded-none [&_img]:w-full [&_img]:my-10 [&_img]:shadow-md
            [&_figcaption]:text-center [&_figcaption]:text-[13px] [&_figcaption]:text-gray-500 [&_figcaption]:mt-3
            [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:mb-8 [&_ul]:space-y-2 [&_li]:text-[18px] [&_li]:md:text-[20px] [&_li]:font-sans [&_li]:text-[#333] [&_li]:whitespace-normal [&_li]:break-words [&_li]:text-left">
        @if(isset($currentChapter))
        <!-- Lấy Previous và Next -->
        @php
        $prevChapter = $article->chapters->where('chapter_number', '<', $currentChapter->chapter_number)->last();
            $nextChapter = $article->chapters->where('chapter_number', '>', $currentChapter->chapter_number)->first();
            @endphp

            <!-- Header Block -->
            <div class="text-center mb-10 not-prose relative z-10">
                <h2 class="text-[22px] md:text-[30px] font-extrabold mb-6 text-gray-800 tracking-tight">Chapter {{ $currentChapter->chapter_number }}: {{ $currentChapter->title }}</h2>

                <!-- Navigation Top -->
                <div class="flex flex-row justify-center items-center gap-2 sm:gap-4">
                    @if($prevChapter)
                    <a href="{{ route('articles.chapter', ['idOrSlug' => $article->id, 'chapterNumber' => $prevChapter->chapter_number]) }}" class="flex items-center justify-center w-[150px] sm:w-[180px] py-2.5 rounded-full bg-[#681313] hover:opacity-90 text-white text-[13px] sm:text-[15px] font-semibold transition-all">
                        <svg class="w-4 h-4 mr-1 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Previous Chapter
                    </a>
                    @else
                    <div class="flex items-center justify-center w-[150px] sm:w-[180px] py-2.5 rounded-full bg-[#681313] opacity-40 text-white text-[13px] sm:text-[15px] font-semibold cursor-not-allowed">
                        <svg class="w-4 h-4 mr-1 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Previous Chapter
                    </div>
                    @endif

                    @if($nextChapter)
                    <a href="{{ route('articles.chapter', ['idOrSlug' => $article->id, 'chapterNumber' => $nextChapter->chapter_number]) }}" class="flex items-center justify-center w-[150px] sm:w-[180px] py-2.5 rounded-full bg-[#681313] hover:opacity-90 text-white text-[13px] sm:text-[15px] font-semibold transition-all">
                        Next Chapter
                        <svg class="w-4 h-4 ml-1 sm:ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    @else
                    <div class="flex items-center justify-center w-[150px] sm:w-[180px] py-2.5 rounded-full bg-[#681313] opacity-40 text-white text-[13px] sm:text-[15px] font-semibold cursor-not-allowed">
                        Next Chapter
                        <svg class="w-4 h-4 ml-1 sm:ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    @endif
                </div>

                <!-- Action Buttons Top -->
                <div class="mt-5 flex justify-center items-center gap-6">
                    <button onclick="toggleChapterDrawer()" class="flex items-center text-sm font-medium text-gray-500 hover:text-[#9d080a] transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        List of Chapters
                    </button>
                </div>
            </div>

            <!-- Bài viết -->
            {!! $currentChapter->content !!}

            <div class="mt-4 md:mt-5 w-full text-center not-prose mb-6">
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

            <!-- Navigation Bottom -->
            <div class="pt-6 flex flex-row justify-center items-center gap-2 sm:gap-4 not-prose border-t border-gray-200">
                @if($prevChapter)
                <a href="{{ route('articles.chapter', ['idOrSlug' => $article->id, 'chapterNumber' => $prevChapter->chapter_number]) }}" class="flex items-center justify-center w-[150px] sm:w-[180px] py-2.5 rounded-full bg-[#681313] hover:opacity-90 text-white text-[13px] sm:text-[15px] font-semibold transition-all">
                    <svg class="w-4 h-4 mr-1 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Previous Chapter
                </a>
                @else
                <div class="flex items-center justify-center w-[150px] sm:w-[180px] py-2.5 rounded-full bg-[#681313] opacity-40 text-white text-[13px] sm:text-[15px] font-semibold cursor-not-allowed">
                    <svg class="w-4 h-4 mr-1 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Previous Chapter
                </div>
                @endif

                @if($nextChapter)
                <a href="{{ route('articles.chapter', ['idOrSlug' => $article->id, 'chapterNumber' => $nextChapter->chapter_number]) }}" class="flex items-center justify-center w-[150px] sm:w-[180px] py-2.5 rounded-full bg-[#681313] hover:opacity-90 text-white text-[13px] sm:text-[15px] font-semibold transition-all">
                    Next Chapter
                    <svg class="w-4 h-4 ml-1 sm:ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                @else
                <div class="flex items-center justify-center w-[150px] sm:w-[180px] py-2.5 rounded-full bg-[#681313] opacity-40 text-white text-[13px] sm:text-[15px] font-semibold cursor-not-allowed">
                    Next Chapter
                    <svg class="w-4 h-4 ml-1 sm:ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
                @endif
            </div>

            <!-- Action Buttons Bottom -->
            @if(isset($currentChapter))
            <div class="mt-6 flex justify-center gap-4 not-prose">
                <button onclick="toggleChapterDrawer()" class="flex items-center text-[13px] font-medium text-gray-500 hover:text-[#9d080a] transition-colors bg-gray-50 px-4 py-2 rounded-lg border border-gray-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    List of Chapters
                </button>
            </div>
            @endif
            @else
            {!! $article->content !!}

            <div class="mt-4 md:mt-5 w-full text-center not-prose">
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
            @endif
    </div>

    <!-- Khối Tiêm Quảng Cáo Động Bằng JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var contentWrapper = document.getElementById('article-content-wrapper');
            if (!contentWrapper) return;

            var pTags = contentWrapper.querySelectorAll(':scope > p');
            var maxAdsToInject = 5; // Tối đa 5 quảng cáo

            var isMobile = window.innerWidth < 768;
            var baseMinParagraphs = isMobile ? 3 : 5; // Tối thiểu 3 hoặc 5 khổ text
            var minLength = isMobile ? 300 : 500; // Cần ít nhất 300 hoặc 500 ký tự

            // Chia đều khoảng cách: tính bước nhảy (step) dựa trên tổng số đoạn văn
            // Dùng Math.max để đảm bảo không bị quá dày đặc (vẫn giữ baseMinParagraphs)
            var step = Math.max(baseMinParagraphs, Math.floor(pTags.length / (maxAdsToInject + 1)));

            var injectedCount = 0;
            var accumulatedLength = 0;
            var paragraphsCount = 0;

            for (var i = 0; i < pTags.length; i++) {
                if (injectedCount >= maxAdsToInject) break;

                var p = pTags[i];
                accumulatedLength += p.textContent.trim().length;
                paragraphsCount++;

                // Điều kiện chèn quảng cáo:
                // 1. Qua đủ số đoạn văn được chia đều (step)
                // 2. Tổng khối lượng chữ đủ dài để tránh chèn vào giữa các câu ngắn
                if (paragraphsCount >= step && accumulatedLength >= minLength) {
                    var adContainer = document.createElement('div');
                    adContainer.className = 'my-6 w-full text-center not-prose inline-ad-container';
                    adContainer.innerHTML = '<ins class="adsbygoogle"' +
                        ' style="display:block"' +
                        ' data-ad-client="ca-pub-4370452252708446"' +
                        ' data-ad-slot="9674028583"' +
                        ' data-ad-format="auto"' +
                        ' data-full-width-responsive="true"></ins>';

                    if (p.nextSibling) {
                        p.parentNode.insertBefore(adContainer, p.nextSibling);
                    } else {
                        p.parentNode.appendChild(adContainer);
                    }

                    try {
                        (window.adsbygoogle = window.adsbygoogle || []).push({});
                        injectedCount++;
                    } catch (e) {}

                    // Reset lại bộ đếm sau khi chèn 1 block Ad
                    accumulatedLength = 0;
                    paragraphsCount = 0;
                }
            }
        });
    </script>

    <!-- Javascript Data for Chapters Drawer -->
    @if(isset($article->chapters) && $article->chapters->count() > 0)
    <!-- Drawer Overlay -->
    <div id="chapter-drawer-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden transition-opacity opacity-0" onclick="toggleChapterDrawer()"></div>

    <!-- Drawer Panel -->
    <div id="chapter-drawer" class="fixed top-0 right-0 h-full w-full sm:w-96 bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Chapters</h3>
            <button onclick="toggleChapterDrawer()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Search -->
        <div class="p-4 border-b border-gray-100 bg-gray-50">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" id="drawer-search" placeholder="Search chapter number or title..." class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-md leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#9d080a] focus:border-[#9d080a] sm:text-sm transition-colors">
            </div>
        </div>

        <!-- List -->
        <div class="flex-1 overflow-y-auto p-4 space-y-2" id="drawer-list">
            <!-- JS will render chapters here -->
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-200 bg-white" id="drawer-pagination">
            <!-- JS will render pagination here -->
        </div>
    </div>

    @php
    $chaptersJsonData = $article->chapters->map(function($ch) use ($article) {
    return [
    'number' => $ch->chapter_number,
    'title' => $ch->title,
    'url' => route('articles.chapter', ['idOrSlug' => $article->id, 'chapterNumber' => $ch->chapter_number])
    ];
    });
    @endphp
    <script id="chapters-data" type="application/json" data-current-chapter="{{ isset($currentChapter) ? $currentChapter->chapter_number : 'null' }}">
        {!! json_encode($chaptersJsonData) !!}
    </script>
    <script>
        window.chaptersData = JSON.parse(document.getElementById('chapters-data').textContent);

        let filteredChapters = [...window.chaptersData];
        let currentPage = 1;
        const itemsPerPage = 10;
        const currentChapterNumberStr = document.getElementById('chapters-data').getAttribute('data-current-chapter');
        const currentChapterNumber = currentChapterNumberStr === 'null' ? null : parseInt(currentChapterNumberStr);

        function toggleChapterDrawer() {
            const drawer = document.getElementById('chapter-drawer');
            const overlay = document.getElementById('chapter-drawer-overlay');
            const body = document.body;

            if (drawer.classList.contains('translate-x-full')) {
                drawer.classList.remove('translate-x-full');
                overlay.classList.remove('hidden');
                // Tiny delay to trigger opacity transition
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
                body.style.overflow = 'hidden'; // Prevent scrolling under drawer

                // Render current state
                renderDrawer();
            } else {
                drawer.classList.add('translate-x-full');
                overlay.classList.add('opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
                body.style.overflow = '';
            }
        }

        function renderDrawer() {
            const listEl = document.getElementById('drawer-list');
            const pagEl = document.getElementById('drawer-pagination');
            listEl.innerHTML = '';
            pagEl.innerHTML = '';

            if (filteredChapters.length === 0) {
                listEl.innerHTML = '<div class="text-center py-8 text-gray-500 text-sm">No chapters found</div>';
                return;
            }

            const totalPages = Math.ceil(filteredChapters.length / itemsPerPage);
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const startIdx = (currentPage - 1) * itemsPerPage;
            const pageItems = filteredChapters.slice(startIdx, startIdx + itemsPerPage);

            // Render List
            pageItems.forEach(ch => {
                const isActive = ch.number == currentChapterNumber;
                const bkgClass = isActive ? 'bg-[#fff5f5] border-[#fbbdbd]' : 'bg-gray-50 border-transparent hover:bg-gray-100';
                const numClass = isActive ? 'text-[#9d080a] font-bold' : 'text-gray-500 font-medium';
                const textClass = isActive ? 'text-[#9d080a] font-bold' : 'text-gray-700';

                listEl.innerHTML += `
                        <a href="${ch.url}" class="flex items-center p-3 rounded border transition-colors ${bkgClass}">
                            <div class="w-12 text-xs mr-3 flex-shrink-0 ${numClass}">
                                Ch. ${ch.number}
                            </div>
                            <div class="flex-1 text-sm truncate ${textClass}">
                                ${ch.title}
                            </div>
                        </a>
                    `;
            });

            // Render Pagination
            if (totalPages > 1) {
                let pagHtml = `<div class="flex items-center justify-center space-x-1">`;

                pagHtml += `<button onclick="goToPage(1)" class="px-2.5 py-1 text-xs border rounded text-gray-500 hover:bg-gray-50" ${currentPage === 1 ? 'disabled class="opacity-50"' : ''}>First</button>`;
                pagHtml += `<button onclick="goToPage(${currentPage - 1})" class="px-2.5 py-1 text-xs border rounded text-gray-500 hover:bg-gray-50" ${currentPage === 1 ? 'disabled class="opacity-50"' : ''}>&lt;</button>`;

                // Show small window of pages around current
                let startPage = Math.max(1, currentPage - 2);
                let endPage = Math.min(totalPages, currentPage + 2);

                for (let p = startPage; p <= endPage; p++) {
                    const activeCls = p === currentPage ? 'bg-[#9d080a] text-white border-[#9d080a] font-bold' : 'text-gray-600 border hover:bg-gray-50';
                    pagHtml += `<button onclick="goToPage(${p})" class="px-3 py-1 text-xs rounded ${activeCls}">${p}</button>`;
                }

                pagHtml += `<button onclick="goToPage(${currentPage + 1})" class="px-2.5 py-1 text-xs border rounded text-gray-500 hover:bg-gray-50" ${currentPage === totalPages ? 'disabled class="opacity-50"' : ''}>&gt;</button>`;
                pagHtml += `<button onclick="goToPage(${totalPages})" class="px-2.5 py-1 text-xs border rounded text-gray-500 hover:bg-gray-50" ${currentPage === totalPages ? 'disabled class="opacity-50"' : ''}>Last (${totalPages})</button>`;

                pagHtml += `</div>`;
                pagEl.innerHTML = pagHtml;
            }
        }

        function goToPage(p) {
            currentPage = p;
            renderDrawer();
        }

        // Search Event
        document.getElementById('drawer-search').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            if (!query) {
                filteredChapters = [...window.chaptersData];
            } else {
                filteredChapters = window.chaptersData.filter(ch => {
                    return ch.title.toLowerCase().includes(query) || ch.number.toString().includes(query);
                });
            }
            currentPage = 1; // reset to first page on search
            renderDrawer();
        });
    </script>
    @endif
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