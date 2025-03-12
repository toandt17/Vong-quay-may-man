<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Vòng quay may mắn cùng Agrijapan - Cơ hội trúng các giải thưởng hấp dẫn">
    <meta name="keywords" content="vòng quay may mắn, agrijapan, quay thưởng, giải thưởng, nông nghiệp">
    <meta property="og:title" content="Vòng Quay May Mắn - {{ $luckyWheel->name }}">
    <meta property="og:description" content="{{ $luckyWheel->description }}">
    <meta property="og:type" content="website">

    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/wheel-preview.jpg') }}">
    <title>Vòng Quay May Mắn - {{ $luckyWheel->name }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/wheel.css') }}">
    <style>
        .wheel-container {
            position: relative;
            perspective: 1000px;
        }

        .wheel-pointer {
            position: absolute;
            top: -30px;
            left: 40%;
            transform: translateX(-50%);
            height: 90px;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
            z-index: 10;
        }

        .wheel-pointer svg {
            fill: var(--festival-red);
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
            transform: rotate(180deg); /* Rotate arrow to point down */
        }

        .prize-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transform: translateY(0);
            transition: all 0.3s ease;
        }

        .prize-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .result-container {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border-radius: 20px;
            padding: 30px;
            margin-top: 40px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            transform: scale(0.95);
            opacity: 0;
            transition: all 0.5s ease;
        }

        .result-container.show {
            transform: scale(1);
            opacity: 1;
        }

        .form-floating {
            position: relative;
            margin-bottom: 20px;
        }

        .form-floating > label {
            position: absolute;
            top: 0;
            left: 0;
            padding: 1rem;
            pointer-events: none;
            transform-origin: 0 0;
            transition: all 0.2s ease;
            color: #6c757d;
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label,
        .form-floating > .form-select:focus ~ label,
        .form-floating > .form-select:not([value=""]) ~ label {
            transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
            background: white;
            padding: 0 0.5rem;
            color: var(--festival-red);
            height: auto;
        }

        .modal-content {
            transform: scale(0.95);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .modal.show .modal-content {
            transform: scale(1);
            opacity: 1;
        }
        html, body {
            overflow: auto !important;
            position: static !important;
        }
    </style>
</head>
<body  style="background-image: url('{{ asset('images/background.jpg') }}'); background-size: cover; background-position: center;">
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-7 col-sm-12 mb-3 mb-md-0">
                    <h1 class="animate__animated animate__fadeInDown">🎉 Vòng Quay May Mắn 🎉</h1>
                    <p class="animate__animated animate__fadeInUp mb-0">{{ $luckyWheel->description }}</p>
                </div>
                <div class="col-md-5 col-sm-12 text-center text-md-end">
                    <button id="open-register-modal" class="btn btn-start animate__animated animate__pulse" style="z-index: 100; position: relative;" onclick="openRegisterForm()">
                        <i class="fas fa-gift me-2"></i> BẮT ĐẦU NGAY
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="row justify-content-center">
            <!-- Vòng quay -->
            <div class="col-12 col-md-8 mx-auto">
                <div class="wheel-section animate__animated animate__fadeIn">
                    <div class="wheel-container">
                        <div class="wheel-wrapper">
                            <div class="wheel" id="wheel">
                                <!-- Các phân đoạn được tạo bằng JavaScript -->
                            </div>
                            <div class="wheel-center">
                                <span>QUAY<br>NGAY!</span>
                            </div>
                        </div>
                       <svg class="wheel-pointer position-absolute" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#FF0000" width="40px" height="40px">
                            <path d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 16.5l-6-6h12z"/>
                        </svg>
                    </div>

                    <button class="spin-button mt-0" id="spin-button" style="background: linear-gradient(135deg, var(--festival-red), var(--lucky-gold));" disabled>
                        <i class="fas fa-sync-alt me-2"></i> QUAY NGAY!
                    </button>

                    <div class="result-container" id="result-container">
                        <h3 id="result-title" class="text-center mb-4"></h3>
                        <p id="result-message" class="text-center"></p>
                        <div class="prize-card" id="prize-details" style="display: none;">
                            <img src="" alt="" class="prize-image mx-auto d-block" id="prize-image">
                            <div class="prize-info text-center mt-4">
                                <h4 id="prize-name" class="mb-3"></h4>
                                <p id="prize-description" class="text-muted"></p>
                                <div class="prize-stats mt-3">
                                    <span id="prize-quantity" class="badge bg-success"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Vòng Quay May Mắn. All rights reserved.</p>
        </div>
    </footer>

    <!-- Modal Đăng Ký -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">
                        <i class="fas fa-user-plus me-2"></i> Đăng ký tham gia
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="registration-form" method="POST">
                        @csrf
                        <input type="hidden" name="lucky_wheel_id" value="{{ $luckyWheel->id }}">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="name" name="name" placeholder=" " required>
                            <label for="name">Họ và tên</label>
                            <div class="invalid-feedback" id="name-error"></div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder=" " required>
                            <label for="phone">Số điện thoại</label>
                            <div class="invalid-feedback" id="phone-error"></div>
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" id="province" name="province" required>
                                <option value="">Chọn Tỉnh/Thành phố</option>
                            </select>
                            <label for="province">Tỉnh/Thành phố</label>
                            <div class="invalid-feedback" id="province-error">Vui lòng chọn Tỉnh/Thành phố</div>
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" id="district" name="district" required disabled>
                                <option value="">Chọn Quận/Huyện</option>
                            </select>
                            <label for="district">Quận/Huyện</label>
                            <div class="invalid-feedback" id="district-error">Vui lòng chọn Quận/Huyện</div>
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" id="ward" name="ward" required disabled>
                                <option value="">Chọn Phường/Xã</option>
                            </select>
                            <label for="ward">Phường/Xã</label>
                            <div class="invalid-feedback" id="ward-error">Vui lòng chọn Phường/Xã</div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="address" name="address" placeholder=" " required>
                            <label for="address">Địa chỉ cụ thể</label>
                            <div class="invalid-feedback" id="address-error">Vui lòng nhập địa chỉ cụ thể</div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input type="checkbox" class="form-check-input" id="is_farmer" name="is_farmer" value="1">
                            <label class="form-check-label" for="is_farmer">Tôi là nông dân</label>
                        </div>

                        <div class="farmer-fields" style="display: none;">
                            <div class="form-group mb-4">
                                <label class="form-label">Giống lúa đang canh tác</label>
                                <select class="form-select custom-select" id="rice_variety" name="rice_variety" required>
                                    <option value="">Chọn giống lúa</option>
                                    <option value="OM5451">OM5451</option>
                                    <option value="Đài thơm 8">Đài thơm 8</option>
                                    <option value="IR50404">IR50404</option>
                                    <option value="OM4900">OM4900</option>
                                    <option value="Jasmine 85">Jasmine 85</option>
                                    <option value="Nàng hoa 9">Nàng hoa 9</option>
                                    <option value="ST24">ST24</option>
                                    <option value="ST25">ST25</option>
                                    <option value="ST 21-3">ST 21-3</option>
                                    <option value="Lúa lai KC06-1">Lúa lai KC06-1</option>
                                    <option value="Lúa giống từ 1 – 3 tháng">Lúa giống từ 1 – 3 tháng</option>
                                    <option value="Lúa giống từ 6 – 8 tháng">Lúa giống từ 6 – 8 tháng</option>
                                    <option value="ML 202 (Ma Lâm 202)">ML 202 (Ma Lâm 202)</option>
                                    <option value="BĐR999">BĐR999</option>
                                    <option value="OM18">OM18</option>
                                    <option value="OM34">OM34</option>
                                    <option value="OM7347">OM7347</option>
                                    <option value="Lúa nếp">Lúa nếp</option>
                                    <option value="Lúa tẻ">Lúa tẻ</option>
                                    <option value="Lúa nàng hai">Lúa nàng hai</option>
                                    <option value="Lúa Thông">Lúa Thông</option>
                                    <option value="Khác">Khác</option>
                                </select>
                                <div class="invalid-feedback" id="rice_variety-error">Vui lòng chọn giống lúa</div>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label">Giai đoạn sinh trưởng</label>
                                <select class="form-select custom-select" id="rice_stage" name="rice_stage" required>
                                    <option value="">Chọn giai đoạn</option>
                                    <option value="Mạ">Mạ</option>
                                    <option value="Đẻ nhánh">Đẻ nhánh</option>
                                    <option value="Đón đồng">Đón đồng</option>
                                    <option value="Làm đồng">Làm đồng</option>
                                    <option value="Lẹt xẹt-trổ đều">Lẹt xẹt-trổ đều</option>
                                    <option value="Sáp-Chín sáp">Sáp-Chín sáp</option>
                                    <option value="Chín">Chín</option>
                                </select>
                                <div class="invalid-feedback" id="rice_stage-error">Vui lòng chọn giai đoạn sinh trưởng</div>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label">Sản phẩm đã sử dụng của AgriJapan</label>
                                <div class="mobile-friendly-select" id="products-container">
                                    <div class="product-option">
                                        <input type="checkbox" id="product-none" name="used_products[]" value="Chưa sử dụng" checked>
                                        <label for="product-none">Chưa sử dụng</label>
                                    </div>
                                    <!-- Bổ sung sản phẩm mới -->
                                    <div class="product-option">
                                        <input type="checkbox" id="product-bgp-choi-to" name="used_products[]" value="Bộ Giải Pháp Chồi To Cây Khỏe">
                                        <label for="product-bgp-choi-to">Bộ Giải Pháp Chồi To Cây Khỏe</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-bgp-dong-bu" name="used_products[]" value="Bộ Giải Pháp Đòng Bự Bông Kẹo">
                                        <label for="product-bgp-dong-bu">Bộ Giải Pháp Đòng Bự Bông Kẹo</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-bgp-sach-khuan" name="used_products[]" value="Bộ Giải Pháp Sạch Khuẩn Sáng Bông">
                                        <label for="product-bgp-sach-khuan">Bộ Giải Pháp Sạch Khuẩn Sáng Bông</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-bgp-tru-benh" name="used_products[]" value="Bộ Giải Pháp Trừ Bệnh AgriJapan">
                                        <label for="product-bgp-tru-benh">Bộ Giải Pháp Trừ Bệnh AgriJapan</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-bgp-tuyet-chieu" name="used_products[]" value="Bộ Giải Pháp Tuyệt Chiêu Nấm Khuẩn">
                                        <label for="product-bgp-tuyet-chieu">Bộ Giải Pháp Tuyệt Chiêu Nấm Khuẩn</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-bgp-dinh-duong" name="used_products[]" value="Bộ Giải Pháp Dinh Dưỡng Phục Hồi Siêu Tốc">
                                        <label for="product-bgp-dinh-duong">Bộ Giải Pháp Dinh Dưỡng Phục Hồi Siêu Tốc</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-bgp-tru-ray" name="used_products[]" value="Bộ Giải Pháp Trừ Rầy AgriJapan">
                                        <label for="product-bgp-tru-ray">Bộ Giải Pháp Trừ Rầy AgriJapan</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-bgp-hoan-hao" name="used_products[]" value="Bộ Giải Pháp Hoàn Hảo Tạo Hạt Thần Tốc">
                                        <label for="product-bgp-hoan-hao">Bộ Giải Pháp Hoàn Hảo Tạo Hạt Thần Tốc</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-bgp-tro-thoat" name="used_products[]" value="Bộ Giải Pháp Trổ Thoát Kẹo Bông">
                                        <label for="product-bgp-tro-thoat">Bộ Giải Pháp Trổ Thoát Kẹo Bông</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-bgp-sach-nam" name="used_products[]" value="Bộ Giải Pháp Sạch Nấm Khuẩn Gốc">
                                        <label for="product-bgp-sach-nam">Bộ Giải Pháp Sạch Nấm Khuẩn Gốc</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-brass-481" name="used_products[]" value="Điều Hòa Sinh Trưởng BRASS 481">
                                        <label for="product-brass-481">Điều Hòa Sinh Trưởng BRASS 481</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-dh-sinh-truong" name="used_products[]" value="Điều hòa sinh trưởng AgriJapan">
                                        <label for="product-dh-sinh-truong">Điều hòa sinh trưởng AgriJapan</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-gibber" name="used_products[]" value="GIBBER 40WG – GABA CỐM">
                                        <label for="product-gibber">GIBBER 40WG – GABA CỐM</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-acgabacyto" name="used_products[]" value="ACGABACYTO 50TB – GABA VIÊN">
                                        <label for="product-acgabacyto">ACGABACYTO 50TB – GABA VIÊN</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-lk-gabacyto" name="used_products[]" value="LK.GABACYTO">
                                        <label for="product-lk-gabacyto">LK.GABACYTO</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-kem-armor" name="used_products[]" value="KẼM ARMOR, KẼM BÁC SĨ (LK-ZN ARMOR)">
                                        <label for="product-kem-armor">KẼM ARMOR, KẼM BÁC SĨ (LK-ZN ARMOR)</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-kem-xanh" name="used_products[]" value="KẼM XANH, KẼM ARMOR (LK-ZN ARMOR)">
                                        <label for="product-kem-xanh">KẼM XANH, KẼM ARMOR (LK-ZN ARMOR)</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-ac-superpotas" name="used_products[]" value="AC-SUPERPOTAS (KALI SỮA 30%)">
                                        <label for="product-ac-superpotas">AC-SUPERPOTAS (KALI SỮA 30%)</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-kali-sua" name="used_products[]" value="KALI SỮA ÔNG GIÀ (LK-K-Ca)">
                                        <label for="product-kali-sua">KALI SỮA ÔNG GIÀ (LK-K-Ca)</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-ac-amino-bo" name="used_products[]" value="AC-AMINO-BO (SỮA ĐẬM ĐẶC)">
                                        <label for="product-ac-amino-bo">AC-AMINO-BO (SỮA ĐẬM ĐẶC)</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-arigold-620" name="used_products[]" value="ARIGOLD 620 (LÂN HỮU HIỆU HAI CHIỀU)">
                                        <label for="product-arigold-620">ARIGOLD 620 (LÂN HỮU HIỆU HAI CHIỀU)</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-arigod" name="used_products[]" value="Arigod">
                                        <label for="product-arigod">Arigod</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-bacla" name="used_products[]" value="ĐẶC TRỊ VI KHUẨN BACLA 50SC">
                                        <label for="product-bacla">ĐẶC TRỊ VI KHUẨN BACLA 50SC</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-starsuper" name="used_products[]" value="STARSUPER 21SL">
                                        <label for="product-starsuper">STARSUPER 21SL</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-omega-downyrust" name="used_products[]" value="OMEGA-DOWNYRUST 48WG">
                                        <label for="product-omega-downyrust">OMEGA-DOWNYRUST 48WG</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-zipra" name="used_products[]" value="ZIPRA 80WP">
                                        <label for="product-zipra">ZIPRA 80WP</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-lk-villa" name="used_products[]" value="LK-VILLA 450SC">
                                        <label for="product-lk-villa">LK-VILLA 450SC</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-aha-500sc" name="used_products[]" value="AHA 500SC">
                                        <label for="product-aha-500sc">AHA 500SC</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-forwavil" name="used_products[]" value="FORWAVIL 5SC">
                                        <label for="product-forwavil">FORWAVIL 5SC</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-gp-dao-on" name="used_products[]" value="GIẢI PHÁP ĐẠO ÔN – VI KHUẨN (BIMDOWMY 375SC + STAR SUPER 21SL)">
                                        <label for="product-gp-dao-on">GIẢI PHÁP ĐẠO ÔN – VI KHUẨN (BIMDOWMY 375SC + STAR SUPER 21SL)</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-bimdowmy" name="used_products[]" value="BIMDOWMY 375SC">
                                        <label for="product-bimdowmy">BIMDOWMY 375SC</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-ronado" name="used_products[]" value="RONADO 500EC">
                                        <label for="product-ronado">RONADO 500EC</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-lorcy" name="used_products[]" value="LORCY 265SC">
                                        <label for="product-lorcy">LORCY 265SC</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-omega-spidermite" name="used_products[]" value="OMEGA-SPIDERMITE 24SC">
                                        <label for="product-omega-spidermite">OMEGA-SPIDERMITE 24SC</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-binhfos" name="used_products[]" value="BINHFOS 50EC">
                                        <label for="product-binhfos">BINHFOS 50EC</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-alex" name="used_products[]" value="ALEX 20SC nhện gié">
                                        <label for="product-alex">ALEX 20SC nhện gié</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-vk-superlau" name="used_products[]" value="VK.SUPERLAU 750WG">
                                        <label for="product-vk-superlau">VK.SUPERLAU 750WG</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-redmine" name="used_products[]" value="REDMINE 500SC">
                                        <label for="product-redmine">REDMINE 500SC</label>
                                    </div>
                                    <div class="product-option">
                                        <input type="checkbox" id="product-gp-ray-canh-trang" name="used_products[]" value="Giải pháp Rầy cánh trắng (Bọ phấn trắng) TIFENA 300SC">
                                        <label for="product-gp-ray-canh-trang">Giải pháp Rầy cánh trắng (Bọ phấn trắng) TIFENA 300SC</label>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Có thể chọn nhiều sản phẩm</small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Đóng
                    </button>
                    <button type="button" class="btn btn-primary" id="submit-registration">
                        <i class="fas fa-paper-plane me-2"></i> ĐĂNG KÝ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Hiển Thị Kết Quả -->
    <div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="result-modal-header">
                    <h5 class="modal-title" id="resultModalLabel">Kết quả quay thưởng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <h3 id="modal-result-title" class="mb-4"></h3>
                    <p id="modal-result-message"></p>
                    <div class="prize-card" id="modal-prize-details" style="display: none;">
                        <img src="" alt="" class="prize-image mx-auto d-block" id="modal-prize-image">
                        <div class="prize-info text-center mt-4">
                            <h4 id="modal-prize-name" class="mb-3"></h4>
                            <p id="modal-prize-description" class="text-muted"></p>
                            <div class="prize-stats mt-3">
                                <span id="modal-prize-quantity" class="badge bg-success"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Lỗi Số Điện Thoại Đã Được Sử Dụng -->
    <div class="modal fade" id="phoneErrorModal" tabindex="-1" aria-labelledby="phoneErrorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content" style="border: 1px solid #f5c6cb;">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-light" id="phoneErrorModalLabel" style="font-size: 16px;">
                        <i class="fas fa-info-circle me-2"></i> Thông báo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <p class="mb-2">Số điện thoại này đã được sử dụng.</p>
                    <p class="small text-muted mb-0">Vui lòng dùng số điện thoại khác.</p>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Đồng ý</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery & Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Debug Modal -->
    <script>
        console.log('==== DEBUG MODAL ====');

        // Kiểm tra jQuery
        console.log('jQuery loaded:', typeof jQuery !== 'undefined', jQuery ? jQuery.fn.jquery : 'not loaded');

        // Kiểm tra Bootstrap
        console.log('Bootstrap loaded:', typeof bootstrap !== 'undefined');
        console.log('Bootstrap Modal loaded:', typeof bootstrap !== 'undefined' && typeof bootstrap.Modal !== 'undefined');

        // Kiểm tra các phần tử DOM
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM fully loaded');
            const modalElement = document.getElementById('registerModal');
            const openBtn = document.getElementById('open-register-modal');
            const spinBtn = document.getElementById('spin-button');

            console.log('Modal element:', modalElement);
            console.log('Open button:', openBtn);
            console.log('Spin button:', spinBtn);

            if (modalElement) {
                console.log('Modal HTML:', modalElement.outerHTML.substring(0, 200) + '...');
            }
        });
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Confetti JS (hiệu ứng pháo hoa) -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <!-- Biến JS để truyền dữ liệu cho script -->
    <script>
        // Biến quản lý trạng thái
        let participantId = null;
        let isRegistered = false;

        // Khai báo biến cho wheel.js
        const wheelData = {
            prizes: @json($prizes)
        };
        const registerUrl = "{{ route('register') }}";
        const spinUrl = "{{ route('spin') }}";
        const luckyWheelId = {{ $luckyWheel->id }};
        const apiBaseUrl = "{{ url('api') }}";
        const provincesUrl = "{{ route('api.provinces') }}";

        // Cấu hình vòng quay
        const wheelConfig = {
            // Bật debug khi cần kiểm tra
            debug: false,
            // Cấu hình hiệu ứng quay
            spinEffect: {
                // Số vòng quay cố định
                rotations: 5,
                // Thời gian quay (milliseconds)
                duration: 5000
            },
            // Điều chỉnh góc offset (bằng số phân đoạn) để mũi tên trỏ đúng phân đoạn
            // Giá trị 0 nghĩa là không dịch chuyển phân đoạn
            // Giá trị 0.5 nghĩa là dịch chuyển nửa phân đoạn
            angleOffset: 0,
            // Đảm bảo mũi tên trỏ chính xác vào giải thưởng
            ensureExactPointer: true
        };

        // Hàm mở form đăng ký
        function openRegisterForm() {
            console.log('Đang mở form đăng ký');

            // Đảm bảo không có backdrop cũ
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                console.log('Xóa backdrop cũ');
                backdrop.parentNode.removeChild(backdrop);
            });

            // Đặt lại các thuộc tính body
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';

            // Tìm modal element
            const registerModalEl = document.getElementById('registerModal');
            if (!registerModalEl) {
                console.error('Không tìm thấy #registerModal');
                return;
            }

            // Thử lại với tất cả các phương pháp có thể
            try {
                console.log('Phương pháp 1: Tạo và hiển thị modal với Bootstrap API');
                new bootstrap.Modal(registerModalEl).show();
            } catch (err) {
                console.error('Lỗi phương pháp 1:', err);

                try {
                    console.log('Phương pháp 2: Tạo modal với tùy chọn cụ thể');
                    const modalOptions = {
                        backdrop: 'static',
                        keyboard: false
                    };
                    new bootstrap.Modal(registerModalEl, modalOptions).show();
                } catch (err2) {
                    console.error('Lỗi phương pháp 2:', err2);

                    if (window.jQuery) {
                        try {
                            console.log('Phương pháp 3: Sử dụng jQuery');
                            $('#registerModal').modal('show');
                        } catch (err3) {
                            console.error('Lỗi phương pháp 3:', err3);

                            try {
                                console.log('Phương pháp 4: Sử dụng data-bs-toggle và click');
                                // Lấy hoặc tạo một nút trigger mới
                                let triggerButton = document.getElementById('hidden-modal-trigger');
                                if (!triggerButton) {
                                    triggerButton = document.createElement('button');
                                    triggerButton.id = 'hidden-modal-trigger';
                                    triggerButton.setAttribute('data-bs-toggle', 'modal');
                                    triggerButton.setAttribute('data-bs-target', '#registerModal');
                                    triggerButton.style.display = 'none';
                                    document.body.appendChild(triggerButton);
                                }
                                triggerButton.click();
                            } catch (err4) {
                                console.error('Lỗi phương pháp 4:', err4);

                                console.log('Phương pháp 5: Thủ công');
                                registerModalEl.classList.add('show');
                                registerModalEl.style.display = 'block';
                                document.body.classList.add('modal-open');

                                // Tạo backdrop thủ công
                                let backdrop = document.createElement('div');
                                backdrop.className = 'modal-backdrop fade show';
                                document.body.appendChild(backdrop);
                            }
                        }
                    }
                }
            }
        }
    </script>

    <!-- Custom JS -->
    <script src="{{ asset('js/wheel.js') }}"></script>

    <!-- Xử lý modal đăng ký -->
    <script>
        console.log('Đang tải script xử lý modal đăng ký');

        // Hàm gọi khi DOM đã sẵn sàng
        function onDOMReady() {
            console.log('DOM đã sẵn sàng - khởi tạo xử lý modal');

            // Tìm các phần tử
            const openModalBtn = document.getElementById('open-register-modal');
            const registerModalEl = document.getElementById('registerModal');

            console.log('Nút mở modal:', openModalBtn);
            console.log('Modal đăng ký:', registerModalEl);

            if (!registerModalEl || !openModalBtn) {
                console.error('Không tìm thấy modal hoặc nút bắt đầu');
                return;
            }

            // Liên kết sự kiện click
            openModalBtn.addEventListener('click', function(e) {
                console.log('Nút đăng ký được nhấn (từ event listener)');
                e.preventDefault();

                openRegisterForm();
            });
        }

        // Đăng ký sự kiện DOMContentLoaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', onDOMReady);
        } else {
            // DOM đã sẵn sàng
            onDOMReady();
        }
    </script>
</body>
</html>
