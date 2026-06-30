@extends('frontend.layouts.app')
@section('title', $product->name . ' - MediaMart')

@section('content')
<div class="page-title-bar">
    <h1 style="font-size:15px;font-weight:500;">{{ Str::limit($product->name, 60) }}</h1>
    <span class="bc ms-auto">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fa fa-chevron-right mx-1" style="font-size:10px;"></i>
        <a href="{{ route('products.index', ['category_id' => $product->category_id]) }}">{{ $product->category->name ?? 'Sản phẩm' }}</a>
        <i class="fa fa-chevron-right mx-1" style="font-size:10px;"></i>
        {{ Str::limit($product->name, 30) }}
    </span>
</div>

<div class="detail-wrap">
    <div class="row g-4">
        {{-- Image --}}
        <div class="col-md-5">
            <img src="{{ $product->photo_url }}" alt="{{ $product->name }}" class="detail-img"
                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'400\'%3E%3Crect width=\'400\' height=\'400\' fill=\'%23f5f5f5\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%23ccc\' font-size=\'60\'%3E📦%3C/text%3E%3C/svg%3E'">
        </div>

        {{-- Info --}}
        <div class="col-md-7">
            <p class="detail-category">
                Danh mục:
                <a href="{{ route('products.index', ['category_id' => $product->category_id]) }}">
                    {{ $product->category->name ?? '' }}
                </a>
            </p>
            <h1 class="detail-name">{{ $product->name }}</h1>

            {{-- Rating summary --}}
            <div class="d-flex align-items-center gap-2 mb-3">
                @php $stars = round($avgRating); @endphp
                <div style="color:#ffc107;font-size:16px;">
                    @for($i=1;$i<=5;$i++)
                        <i class="fa{{ $i <= $stars ? 's' : 'r' }} fa-star"></i>
                    @endfor
                </div>
                <span style="font-size:13px;color:var(--gray-700);">{{ number_format($avgRating,1) }}/5 ({{ $product->ratings->count() }} đánh giá)</span>
            </div>

            {{-- Price --}}
            <div class="detail-price-box">
                @if($product->discount > 0)
                    <div class="detail-price-original">{{ number_format($product->price) }}₫</div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="detail-price-final">{{ number_format($product->final_price) }}₫</span>
                        <span class="detail-discount-badge">-{{ $product->discount }}%</span>
                    </div>
                    <div style="font-size:13px;color:#c62828;margin-top:4px;">
                        Tiết kiệm: {{ number_format($product->price - $product->final_price) }}₫
                    </div>
                @else
                    <div class="detail-price-final">{{ number_format($product->final_price) }}₫</div>
                @endif
            </div>

            <p class="detail-desc">{!! $product->description !!}</p>

            {{-- Actions --}}
            <div class="d-flex gap-2 flex-wrap">
                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-buy">
                        <i class="fa fa-shopping-cart"></i> Thêm vào giỏ hàng
                    </button>
                </form>
                <form action="{{ route('wishlist.add', $product->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-wishlist-lg">
                        <i class="fa fa-heart"></i> Yêu thích
                    </button>
                </form>
            </div>

            {{-- Rating form --}}
            <div class="rating-wrap">
                <h5><i class="fa fa-star" style="color:#ffc107;"></i> Đánh giá sản phẩm</h5>
                <form action="{{ route('products.rating', $product->id) }}" method="POST">
                    @csrf
                    <div class="star-btns" id="starBtns">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" class="star-btn" onclick="selectStar({{ $i }})">
                                {{ $i }} ★
                            </button>
                            <input type="radio" name="star" value="{{ $i }}" id="star{{ $i }}" style="display:none;">
                        @endfor
                    </div>
                    <button type="submit" class="btn-rate">Gửi đánh giá</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Detail content --}}
@if($product->content)
    <div class="detail-content-box">
        <h3>Mô tả chi tiết</h3>
        <div style="line-height:1.8;color:var(--gray-700);">{!! $product->content !!}</div>
    </div>
@endif

{{-- Related products --}}
@if($related->count())
    <div style="margin-top:32px;">
        <div class="mm-section-title">
            <span>Sản phẩm liên quan</span>
        </div>
        <div class="products-grid products-grid-4">
            @foreach($related as $product)
                @include('frontend.partials.product-card')
            @endforeach
        </div>
    </div>
@endif

@push('scripts')
<script>
function selectStar(n) {
    document.querySelectorAll('.star-btn').forEach((btn, i) => {
        btn.classList.toggle('active', i < n);
    });
    document.getElementById('star' + n).checked = true;
}
</script>
@endpush
@endsection
