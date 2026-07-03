@extends('frontend.layouts.app')
@section('title', 'Tin tức công nghệ - MediaMart')

@section('content')
<div class="page-title-bar">
    <h1><i class="fa fa-newspaper" style="color:var(--red);margin-right:8px;"></i>Tin tức công nghệ</h1>
</div>

<div class="news-grid" style="grid-template-columns:repeat(3,1fr);">
    @forelse($articles as $article)
    <div class="news-card">
        <a href="{{ route('news.detail', $article->id) }}">
            <img src="{{ $article->photo_url }}" alt="{{ $article->name }}" loading="lazy">
        </a>
        <div class="news-body">
            @if($article->hot)<span class="news-badge">HOT</span>@endif
            <a href="{{ route('news.detail', $article->id) }}" class="news-title d-block">{{ Str::limit($article->name, 75) }}</a>
            <p class="news-desc mt-2">{{ Str::limit(strip_tags(html_entity_decode($article->description)), 100) }}</p>
            <a href="{{ route('news.detail', $article->id) }}" style="color:var(--red);font-size:13px;font-weight:600;">
                Đọc tiếp <i class="fa fa-arrow-right"></i>
            </a>
        </div>
    </div>
    @empty
        <div style="grid-column:1/-1;" class="empty-state">
            <div class="empty-icon">📰</div>
            <p>Chưa có tin tức nào.</p>
        </div>
    @endforelse
</div>
<div>{{ $articles->links() }}</div>
@endsection
