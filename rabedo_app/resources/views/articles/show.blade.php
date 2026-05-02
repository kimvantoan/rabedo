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
<article class="article-container">
    <h1 class="article-main-title">
        {{ $article->title }}
    </h1>
    <div class="ads-wrapper-top-show">
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
    <p class="article-desc-p">
        {{ $article->description }}
    </p>
    @endif
    @endif

    @if(!(isset($currentChapter) && $currentChapter->chapter_number >= 2))
    <div class="article-meta-wrapper">
        <span class="article-meta-label">By</span>
        <span class="article-meta-author">{{ $article->author ?: 'Admin' }}</span>
        <span class="article-meta-divider">—</span>
        <span class="article-meta-date">{{ \Carbon\Carbon::parse($article->created_at)->format('M d, Y') }}</span>
    </div>
    @endif

    @if(!(isset($currentChapter) && $currentChapter->chapter_number >= 2))
    @if(!empty($article->thumbnail))
    <div class="article-img-wrapper">
        <img src="{{ asset($article->thumbnail) }}" alt="{{ $article->title }}" class="article-img" loading="eager">
    </div>
    @endif
    @endif

    <div id="article-content-wrapper">
        @if(isset($currentChapter))
        <!-- Lấy Previous và Next -->
        @php
        $prevChapter = $article->chapters->where('chapter_number', '<', $currentChapter->chapter_number)->last();
            $nextChapter = $article->chapters->where('chapter_number', '>', $currentChapter->chapter_number)->first();
            @endphp

            <!-- Header Block -->
            <div class="chapter-header-container">
                <h2 class="article-chapter-title">Chapter {{ $currentChapter->chapter_number }}: {{ $currentChapter->title }}</h2>

                <!-- Navigation Top -->
                <div class="chapter-nav-top-wrapper">
                    @if($prevChapter)
                    <a href="{{ route('articles.chapter', ['idOrSlug' => $article->id, 'chapterNumber' => $prevChapter->chapter_number]) }}" class="chapter-nav-btn">
                        <svg class="chapter-nav-icon-prev-inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Previous Chapter
                    </a>
                    @else
                    <div class="chapter-nav-btn-disabled">
                        <svg class="chapter-nav-icon-prev-inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Previous Chapter
                    </div>
                    @endif

                    @if($nextChapter)
                    <a href="{{ route('articles.chapter', ['idOrSlug' => $article->id, 'chapterNumber' => $nextChapter->chapter_number]) }}" class="chapter-nav-btn">
                        Next Chapter
                        <svg class="chapter-nav-icon-next-inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    @else
                    <div class="chapter-nav-btn-disabled">
                        Next Chapter
                        <svg class="chapter-nav-icon-next-inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    @endif
                </div>

                <!-- Action Buttons Top -->
                <div class="chapter-action-top-wrapper">
                    <button onclick="toggleChapterDrawer()" class="chapter-list-btn-top">
                        <svg class="chapter-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        List of Chapters
                    </button>
                </div>
            </div>

            <!-- Bài viết -->
            <div class="rabedo-prose">
                {!! $currentChapter->content !!}
            </div>



            <!-- Navigation Bottom -->
            <div class="chapter-nav-bottom-wrapper">
                @if($prevChapter)
                <a href="{{ route('articles.chapter', ['idOrSlug' => $article->id, 'chapterNumber' => $prevChapter->chapter_number]) }}" class="chapter-nav-btn">
                    <svg class="chapter-nav-icon-prev-inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Previous Chapter
                </a>
                @else
                <div class="chapter-nav-btn-disabled">
                    <svg class="chapter-nav-icon-prev-inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Previous Chapter
                </div>
                @endif

                @if($nextChapter)
                <a href="{{ route('articles.chapter', ['idOrSlug' => $article->id, 'chapterNumber' => $nextChapter->chapter_number]) }}" class="chapter-nav-btn">
                    Next Chapter
                    <svg class="chapter-nav-icon-next-inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                @else
                <div class="chapter-nav-btn-disabled">
                    Next Chapter
                    <svg class="chapter-nav-icon-next-inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
                @endif
            </div>

            <!-- Action Buttons Bottom -->
            @if(isset($currentChapter))
            <div class="chapter-action-bottom-wrapper">
                <button onclick="toggleChapterDrawer()" class="chapter-list-btn-bottom">
                    <svg class="chapter-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    List of Chapters
                </button>
            </div>
            @endif
            @else
            <div class="rabedo-prose">
                {!! $article->content !!}
            </div>
            @endif
    </div>



    <!-- Javascript Data for Chapters Drawer -->
    @if(isset($article->chapters) && $article->chapters->count() > 0)
    <!-- Drawer Overlay -->
    <div id="chapter-drawer-overlay" class="drawer-overlay" onclick="toggleChapterDrawer()"></div>

    <!-- Drawer Panel -->
    <div id="chapter-drawer" class="drawer-panel">
        <!-- Header -->
        <div class="drawer-header">
            <h3 class="drawer-title">Chapters</h3>
            <button onclick="toggleChapterDrawer()" class="drawer-close-btn">
                <svg class="drawer-close-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Search -->
        <div class="drawer-search-wrapper">
            <div class="drawer-search-inner">
                <div class="drawer-search-icon-wrapper">
                    <svg class="drawer-search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" id="drawer-search" placeholder="Search chapter number or title..." class="drawer-search-input">
            </div>
        </div>

        <!-- List -->
        <div class="drawer-list" id="drawer-list">
            <!-- JS will render chapters here -->
        </div>

        <!-- Pagination -->
        <div class="drawer-pagination-wrapper" id="drawer-pagination">
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
                const bkgClass = isActive ? 'drawer-list-item-active' : 'drawer-list-item-inactive';
                const numClass = isActive ? 'drawer-list-num-active' : 'drawer-list-num-inactive';
                const textClass = isActive ? 'drawer-list-title-active' : 'drawer-list-title-inactive';

                listEl.innerHTML += `
                        <a href="${ch.url}" class="drawer-list-item ${bkgClass}">
                            <div class="drawer-list-num ${numClass}">
                                Ch. ${ch.number}
                            </div>
                            <div class="drawer-list-title ${textClass}">
                                ${ch.title}
                            </div>
                        </a>
                    `;
            });

            // Render Pagination
            if (totalPages > 1) {
                let pagHtml = `<div class="drawer-pag-container">`;

                pagHtml += `<button onclick="goToPage(1)" class="drawer-pag-btn-edge" ${currentPage === 1 ? 'disabled' : ''}>First</button>`;
                pagHtml += `<button onclick="goToPage(${currentPage - 1})" class="drawer-pag-btn-edge" ${currentPage === 1 ? 'disabled' : ''}>&lt;</button>`;

                // Show small window of pages around current
                let startPage = Math.max(1, currentPage - 2);
                let endPage = Math.min(totalPages, currentPage + 2);

                for (let p = startPage; p <= endPage; p++) {
                    const activeCls = p === currentPage ? 'drawer-pag-btn-active' : 'drawer-pag-btn-page';
                    pagHtml += `<button onclick="goToPage(${p})" class="${activeCls}">${p}</button>`;
                }

                pagHtml += `<button onclick="goToPage(${currentPage + 1})" class="drawer-pag-btn-edge" ${currentPage === totalPages ? 'disabled' : ''}>&gt;</button>`;
                pagHtml += `<button onclick="goToPage(${totalPages})" class="drawer-pag-btn-edge" ${currentPage === totalPages ? 'disabled' : ''}>Last (${totalPages})</button>`;

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
<div class="related-articles-wrapper">
    <div class="section-header">
        <h2 class="section-title">
            Related Articles
        </h2>
    </div>

    <div class="article-grid">
        @foreach($relatedArticles as $relArticle)
        <article class="article-card">
            @if($relArticle->thumbnail)
            <div class="article-card-img-wrapper">
                <img src="{{ asset($relArticle->thumbnail) }}" alt="{{ $relArticle->title }}" class="article-card-img" loading="lazy">
            </div>
            @endif
            <div class="article-card-content">
                <div class="article-card-date">
                    <time datetime="{{ $relArticle->created_at }}">
                        {{ \Carbon\Carbon::parse($relArticle->created_at)->format('M d, Y') }}
                    </time>
                </div>
                <div class="article-card-title-wrapper">
                    <h3 class="article-card-title">
                        <a href="{{ rtrim(config('app.url'), '/') . route('articles.show', ['idOrSlug' => $relArticle->id], false) }}/?utm_source={{ $relArticle->user?->username ?? 'admin' }}&utm_medium=social">
                            <span class="article-card-link-overlay"></span>
                            {{ $relArticle->title }}
                        </a>
                    </h3>
                </div>
                <div class="article-card-desc">
                    {{ \Illuminate\Support\Str::limit(strip_tags($relArticle->content), 120) }}
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
</div>
@endif
@endsection