@extends('frontend.layouts.app')
@section('title', 'MediaMart - Điện tử & Công nghệ hàng đầu')

@section('content')

{{-- Sản phẩm HOT --}}
<div class="mm-section-title">
    <span><i class="fa fa-fire" style="color:var(--red);margin-right:6px;"></i>SẢN PHẨM HOT</span>
</div>
<div class="products-grid" style="margin-bottom:32px;">
    @foreach($hotProducts as $product)
        @include('frontend.partials.product-card')
    @endforeach
</div>

{{-- Banner --}}
<div class="mm-banner">
    <img src="{{ asset('assets/frontend/images/adv1.jpg') }}" alt="Banner" onerror="this.style.display='none'">
</div>

{{-- Danh mục sản phẩm --}}
@foreach($categories as $category)
    <div style="margin-top:36px;">
        <div class="mm-section-title">
            <span>{{ $category->name }}</span>
            <a href="{{ route('products.index', ['category_id' => $category->id]) }}" class="view-all">
                Xem tất cả <i class="fa fa-arrow-right"></i>
            </a>
        </div>
        <div class="products-grid">
            @foreach(($category->children->count() ? $category->children->flatMap->products : $category->products)->take(6) as $product)
                @include('frontend.partials.product-card')
            @endforeach
        </div>
    </div>
@endforeach

{{-- Tin tức --}}
@if($hotNews->count())
    <div style="margin-top:40px;">
        <div class="mm-section-title">
            <span><i class="fa fa-newspaper" style="color:var(--red);margin-right:6px;"></i>TIN TỨC NỔI BẬT</span>
            <a href="{{ route('news.index') }}" class="view-all">Xem tất cả <i class="fa fa-arrow-right"></i></a>
        </div>
        <div class="news-grid">
            @foreach($hotNews as $article)
                <div class="news-card">
                    <a href="{{ route('news.detail', $article->id) }}">
                        <img src="{{ $article->photo_url }}" alt="{{ $article->name }}" loading="lazy">
                    </a>
                    <div class="news-body">
                        @if($article->hot)<span class="news-badge">HOT</span>@endif
                        <a href="{{ route('news.detail', $article->id) }}" class="news-title d-block">{{ Str::limit($article->name, 65) }}</a>
                        <p class="news-desc mt-2">{{ Str::limit(strip_tags(html_entity_decode($article->description)), 85) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@endsection
