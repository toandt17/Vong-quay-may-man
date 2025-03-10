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
}

.wheel-pointer {
    top: 0;
    left: 46%; /* Di chuyển mũi tên sang trái */
    transform: translateX(-50%);
    fill: red;
}
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <h1 class="animate__animated animate__fadeInDown">Vòng Quay May Mắn</h1>
            <p class="animate__animated animate__fadeInUp">{{ $luckyWheel->description }}</p>
        </div>
    </header>

    <div class="container">
        <div class="row justify-content-center">
            <!-- Nút mở form đăng ký -->
            <div class="col-12 col-md-6 mb-4">
                <div class="start-container animate__animated animate__fadeInLeft">
                    <h2 class="start-title">Tham gia ngay</h2>
                    <p>Đăng ký tham gia và có cơ hội nhận những phần quà hấp dẫn!</p>
                    <button id="open-register-modal" class="btn btn-primary btn-lg">BẮT ĐẦU NGAY</button>
                </div>
            </div>

            <!-- Vòng quay -->
            <div class="col-12 col-md-6">
                <div class="wheel-section animate__animated animate__fadeInRight">
                    <div class="wheel-container position-relative">
                        <div class="wheel-wrapper">
                            <div class="wheel" id="wheel">
                                <!-- Các phân đoạn được tạo bằng JavaScript -->
                            </div>
                            <div class="wheel-center">
                                <span>QUAY<br>NGAY!</span>
                            </div>
                        </div>
                        <!-- Thay thế hình ảnh mũi tên bằng mã SVG và căn chỉnh bằng CSS -->
                        <svg class="wheel-pointer position-absolute" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24px" height="24px">
                            <path d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 16.5l-6-6h12z"/>
                        </svg>
                    </div>

                    <button class="spin-button" id="spin-button" disabled>QUAY NGAY!</button>

                    <div class="result-container" id="result-container">
                        <h3 id="result-title"></h3>
                        <p id="result-message"></p>
                        <div class="prize-details" id="prize-details" style="display: none;">
                            <img src="" alt="" class="prize-image" id="prize-image">
                            <div class="prize-info">
                                <h4 id="prize-name"></h4>
                                <p id="prize-description"></p>
                                <div class="prize-stats">
                                    <span id="prize-quantity"></span>
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
                    <h5 class="modal-title" id="registerModalLabel">Đăng ký tham gia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="registration-form">
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>

                        <div class="mb-3">
                            <label for="province" class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                            <select class="form-select" id="province" name="province" required>
                                <option value="">Chọn Tỉnh/Thành phố</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="district" class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                            <select class="form-select" id="district" name="district" required disabled>
                                <option value="">Chọn Quận/Huyện</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="ward" class="form-label">Phường/Xã <span class="text-danger">*</span></label>
                            <select class="form-select" id="ward" name="ward" required disabled>
                                <option value="">Chọn Phường/Xã</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ cụ thể <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_farmer" name="is_farmer" value="1">
                            <label class="form-check-label" for="is_farmer">Tôi là nông dân</label>
                        </div>

                        <div class="mb-3 farmer-field" style="display: none;">
                            <label for="rice_variety" class="form-label">Giống lúa đang canh tác</label>
                            <input type="text" class="form-control" id="rice_variety" name="rice_variety">
                        </div>

                        <div class="mb-3">
                            <label for="used_products" class="form-label">Sản phẩm đã sử dụng của Agrijapan</label>
                            <input type="text" class="form-control" id="used_products" name="used_products">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="submit-registration">ĐĂNG KÝ</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Biến JS để truyền dữ liệu cho script -->
    <script>
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
            // Tắt debug khi đã cài đặt xong
            debug: false,
            // Cấu hình hiệu ứng quay
            spinEffect: {
                // Số vòng quay cố định
                rotations: 5,
                // Thời gian quay (milliseconds)
                duration: 5000
            },
            // Điều chỉnh góc offset (bằng độ) để mũi tên trỏ đúng phân đoạn
            // Giá trị -1 nghĩa là dịch chuyển ngược lại 1 phân đoạn (nếu lấy giải bên phải)
            angleOffset: -1
        };
    </script>

    <!-- Custom JS -->
    <script src="{{ asset('js/wheel.js') }}"></script>

    <!-- Animation Script -->
    <script>
        // Extra animation for wheel after registration
        document.addEventListener('DOMContentLoaded', function() {
            // Add animate.css library classes to wheel section
            const wheelSection = document.querySelector('.wheel-section');

            // Remove initial animation classes
            setTimeout(() => {
                wheelSection.classList.remove('animate__animated', 'animate__fadeInRight');
            }, 1000);

            // Function to apply animation to wheel section after registration
            window.animateWheelAfterRegistration = function() {
                // Add attention animation
                wheelSection.classList.add('animate__animated', 'animate__pulse');

                // Remove after animation completes
                setTimeout(() => {
                    wheelSection.classList.remove('animate__animated', 'animate__pulse');
                }, 1000);
            };
        });
    </script>
</body>
</html>
