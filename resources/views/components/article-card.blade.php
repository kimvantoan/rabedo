@props(['article', 'isRelated' => false])

@php
    if ($isRelated) {
        $url = rtrim(config('app.url'), '/') . route('articles.show', ['idOrSlug' => $article->id], false) . '/?utm_source=' . ($article->user?->username ?? 'admin') . '&utm_medium=social';
    } else {
        $url = route('articles.show', [$article->id]);
    }
    $description = \Illuminate\Support\Str::limit(strip_tags($article->description ?: $article->content), 120);
@endphp

<article class="article-card">
    @if($article->thumbnail)
    <div class="article-card-img-wrapper">
        <a href="{{ $url }}" class="block w-full h-full">
            <img src="{{ asset($article->thumbnail) }}" alt="{{ $article->title }}" class="article-card-img object-cover w-full h-full" loading="lazy">
        </a>
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
                {{ $article->title }}
            </h3>
        </div>
        <div class="article-card-desc">
            {{ $description }}
        </div>
        <div class="article-card-footer">
            <a href="{{ $url }}" class="article-card-readmore">
                Read Article
                <svg xmlns="http://www.w3.org/2000/svg" class="article-card-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    </div>
</article>
