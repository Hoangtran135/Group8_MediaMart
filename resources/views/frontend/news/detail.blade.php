@extends('frontend.layouts.app')
@section('title', $article->name . ' - MediaMart')

@section('content')
<div class="row g-4">
    {{-- Article content --}}
    <div class="col-md-8">
        <div class="detail-content-box" style="padding:28px;">
            <p style="color:var(--gray-500);font-size:13px;margin-bottom:8px;">
                <i class="fa fa-calendar me-1"></i> {{ $article->created_at->format('d/m/Y') }}
                &nbsp;|&nbsp; <i class="fa fa-tag me-1"></i> Tin tức công nghệ
            </p>
            <h1 style="font-size:22px;font-weight:700;margin-bottom:16px;line-height:1.4;">{{ $article->name }}</h1>
            @if($article->photo)
                <img src="{{ $article->photo_url }}" alt="{{ $article->name }}"
                     style="width:100%;max-height:420px;object-fit:cover;border-radius:8px;margin-bottom:20px;">
            @endif
            <div id="article-content" style="line-height:1.85;color:var(--gray-700);">{!! $article->content !!}</div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-md-4">
        <div style="background:#fff;border-radius:var(--radius);padding:20px;box-shadow:var(--shadow-sm);">
            <h5 style="font-weight:700;margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid var(--red);">
                Tin tức khác
            </h5>
            @foreach($related as $item)
            <a href="{{ route('news.detail', $item->id) }}" class="d-flex gap-3 mb-3 text-decoration-none"
               style="padding:10px;border-radius:8px;transition:background .2s;" onmouseover="this.style.background='var(--gray-100)'" onmouseout="this.style.background='transparent'">
                <img src="{{ $item->photo_url }}" style="width:72px;height:60px;object-fit:cover;border-radius:6px;flex-shrink:0;">
                <div>
                    <p style="font-size:13px;font-weight:500;color:var(--gray-900);margin:0;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        {{ Str::limit($item->name, 65) }}
                    </p>
                    <span style="font-size:12px;color:var(--gray-500);">{{ $item->created_at->format('d/m/Y') }}</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
// Ảnh trong nội dung bài viết cũ có thể trỏ tới nguồn bên ngoài đã gỡ/đổi
// URL — ẩn thẻ ảnh nếu tải lỗi thay vì hiện icon vỡ.
document.querySelectorAll('#article-content img').forEach(function (img) {
    img.addEventListener('error', function () {
        this.style.display = 'none';
    });
});
</script>
@endpush
@endsection
