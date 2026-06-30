@extends('frontend.layouts.app')
@section('title', 'Liên hệ - MediaMart')

@section('content')
<div class="page-title-bar">
    <h1><i class="fa fa-phone" style="color:var(--red);margin-right:8px;"></i>Liên hệ với chúng tôi</h1>
</div>

<div class="row g-4">
    {{-- Contact info --}}
    <div class="col-md-5">
        <div class="contact-card">
            <h4 style="font-weight:700;margin-bottom:22px;">Thông tin liên hệ</h4>
            <div class="contact-info-item">
                <div class="contact-info-icon"><i class="fa fa-map-marker-alt"></i></div>
                <div>
                    <div style="font-weight:600;margin-bottom:2px;">Địa chỉ</div>
                    <div style="color:var(--gray-700);font-size:14px;">55 Giải Phóng, Hà Nội</div>
                </div>
            </div>
            <div class="contact-info-item">
                <div class="contact-info-icon"><i class="fa fa-phone"></i></div>
                <div>
                    <div style="font-weight:600;margin-bottom:2px;">Hotline</div>
                    <div style="color:var(--red);font-size:16px;font-weight:700;">0378106753</div>
                    <div style="color:var(--gray-500);font-size:12px;">Miễn phí – 8:00 đến 21:00</div>
                </div>
            </div>
            <div class="contact-info-item">
                <div class="contact-info-icon"><i class="fa fa-envelope"></i></div>
                <div>
                    <div style="font-weight:600;margin-bottom:2px;">Email</div>
                    <div style="color:var(--gray-700);font-size:14px;">hoangtranxuan04@gmaill.com</div>
                </div>
            </div>
            <div class="contact-info-item">
                <div class="contact-info-icon"><i class="fa fa-clock"></i></div>
                <div>
                    <div style="font-weight:600;margin-bottom:2px;">Giờ làm việc</div>
                    <div style="color:var(--gray-700);font-size:14px;">Thứ 2 – Thứ 7: 8:00 – 21:00</div>
                </div>
            </div>

            <div style="margin-top:24px;padding:16px;background:var(--red-light);border-radius:8px;">
                <p style="font-size:13px;color:var(--red-dark);margin:0;font-weight:500;">
                    <i class="fa fa-shield-alt me-2"></i>
                    Chúng tôi cam kết phản hồi trong vòng 2 giờ làm việc.
                </p>
            </div>
        </div>
    </div>

    {{-- Contact form --}}
    <div class="col-md-7">
        <div class="contact-card">
            <h4 style="font-weight:700;margin-bottom:22px;">Gửi tin nhắn cho chúng tôi</h4>
            <form>
                <div class="row g-3">
                    <div class="col-md-6 form-group">
                        <label class="form-label">Họ tên</label>
                        <input type="text" class="form-input" placeholder="Nguyễn Văn A">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-input" placeholder="email@gmail.com">
                    </div>
                    <div class="col-12 form-group">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" class="form-input" placeholder="0901 234 567">
                    </div>
                    <div class="col-12 form-group">
                        <label class="form-label">Nội dung</label>
                        <textarea class="form-input" rows="5" placeholder="Mô tả vấn đề của bạn..." style="resize:vertical;"></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn-send">
                            <i class="fa fa-paper-plane me-2"></i>Gửi tin nhắn
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
