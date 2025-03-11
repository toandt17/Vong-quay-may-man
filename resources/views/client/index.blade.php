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
            left: 46%;
            transform: translateX(-50%);
            width: 40px;
            height: 40px;
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
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <h1 class="animate__animated animate__fadeInDown">🎉 Vòng Quay May Mắn 🎉</h1>
            <p class="animate__animated animate__fadeInUp">{{ $luckyWheel->description }}</p>
        </div>
    </header>

    <div class="container">
        <div class="row justify-content-center">
            <!-- Nút mở form đăng ký -->
            <div class="col-12 col-md-6 mb-4">
                <div class="start-container animate__animated animate__fadeInLeft">
                    <h2 class="start-title">🎁 Tham Gia Ngay</h2>
                    <p>Đăng ký tham gia và có cơ hội nhận những phần quà hấp dẫn!</p>
                    <button id="open-register-modal" class="btn btn-primary btn-lg">
                        <i class="fas fa-gift me-2"></i> BẮT ĐẦU NGAY
                    </button>
                </div>
            </div>

            <!-- Vòng quay -->
            <div class="col-12 col-md-6">
                <div class="wheel-section animate__animated animate__fadeInRight">
                    <div class="wheel-container">
                        <div class="wheel-wrapper">
                            <div class="wheel" id="wheel">
                                <!-- Các phân đoạn được tạo bằng JavaScript -->
                            </div>
                            <div class="wheel-center">
                                <span>QUAY<br>NGAY!</span>
                            </div>
                        </div>
                       <svg class="wheel-pointer position-absolute" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24px" height="24px">
                            <path d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 16.5l-6-6h12z"/>
                        </svg>
                    </div>

                    <button class="spin-button" id="spin-button" disabled>
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
                        </div>

                        <div class="form-floating mb-3">
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder=" " required>
                            <label for="phone">Số điện thoại</label>
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" id="province" name="province" required>
                                <option value="">Chọn Tỉnh/Thành phố</option>
                            </select>
                            <label for="province">Tỉnh/Thành phố</label>
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" id="district" name="district" required disabled>
                                <option value="">Chọn Quận/Huyện</option>
                            </select>
                            <label for="district">Quận/Huyện</label>
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" id="ward" name="ward" required disabled>
                                <option value="">Chọn Phường/Xã</option>
                            </select>
                            <label for="ward">Phường/Xã</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="address" name="address" placeholder=" " required>
                            <label for="address">Địa chỉ cụ thể</label>
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
                                    <option value="Khác">Khác</option>
                                </select>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label">Giai đoạn sinh trưởng</label>
                                <select class="form-select custom-select" id="rice_stage" name="rice_stage" required>
                                    <option value="">Chọn giai đoạn</option>
                                    <option value="Chuẩn bị xuống giống">Chuẩn bị xuống giống</option>
                                    <option value="Mạ (1-14 ngày)">Mạ (1-14 ngày)</option>
                                    <option value="Đẻ nhánh (15-35 ngày)">Đẻ nhánh (15-35 ngày)</option>
                                    <option value="Đứng cái (35-45 ngày)">Đứng cái (35-45 ngày)</option>
                                    <option value="Làm đòng (45-55 ngày)">Làm đòng (45-55 ngày)</option>
                                    <option value="Trổ bông (55-85 ngày)">Trổ bông (55-85 ngày)</option>
                                    <option value="Chín (85-110 ngày)">Chín (85-110 ngày)</option>
                                </select>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label">Sản phẩm đã sử dụng của AgriJapan</label>
                                <select class="form-select custom-select" id="used_products" name="used_products" multiple>
                                    <option value="Chưa sử dụng" selected>Chưa sử dụng</option>
                                    <option value="Phân bón lá">Phân bón lá</option>
                                    <option value="Phân bón rễ">Phân bón rễ</option>
                                    <option value="Thuốc bảo vệ thực vật">Thuốc bảo vệ thực vật</option>
                                    <option value="Kích thích sinh trưởng">Kích thích sinh trưởng</option>
                                </select>
                                <small class="form-text text-muted">Có thể chọn nhiều sản phẩm (giữ phím Ctrl hoặc Cmd)</small>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        document.addEventListener('DOMContentLoaded', function() {
            // Khởi tạo modal Bootstrap
            const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));

            // Xử lý mở modal
            document.getElementById('open-register-modal').addEventListener('click', function() {
                // Reset các backdrop trước khi mở modal mới
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';

                // Mở modal
                new bootstrap.Modal(document.getElementById('registerModal')).show();
            });

            // Xử lý checkbox nông dân
            const farmerCheckbox = document.getElementById('is_farmer');
            const farmerFields = document.querySelector('.farmer-fields');

            farmerCheckbox.addEventListener('change', function() {
                farmerFields.style.display = this.checked ? 'block' : 'none';
            });

            // Xử lý dropdown giống lúa
            document.querySelectorAll('#rice_variety + select').forEach(select => {
                select.addEventListener('change', function() {
                    const selectedValue = this.value;
                    const selectedText = this.options[this.selectedIndex].text;
                    document.getElementById('rice_variety').value = selectedValue;
                });
            });

            // Xử lý dropdown giai đoạn lúa
            document.querySelectorAll('#rice_stage + select').forEach(select => {
                select.addEventListener('change', function() {
                    const selectedValue = this.value;
                    const selectedText = this.options[this.selectedIndex].text;
                    document.getElementById('rice_stage').value = selectedValue;
                });
            });

            // Xử lý tỉnh/thành phố
            fetch(provincesUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const provinceSelect = document.getElementById('province');
                        data.data.forEach(province => {
                            const option = document.createElement('option');
                            option.value = province.name;
                            option.textContent = province.name;
                            option.dataset.id = province.id;
                            provinceSelect.appendChild(option);
                        });
                    }
                });

            // Xử lý quận/huyện
            document.getElementById('province').addEventListener('change', function() {
                const provinceId = this.options[this.selectedIndex].dataset.id;
                const districtSelect = document.getElementById('district');
                const wardSelect = document.getElementById('ward');

                // Reset quận/huyện và phường/xã
                districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
                wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                districtSelect.disabled = true;
                wardSelect.disabled = true;

                if (provinceId) {
                    fetch(`${apiBaseUrl}/provinces/${provinceId}/districts`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                data.data.forEach(district => {
                                    const option = document.createElement('option');
                                    option.value = district.name;
                                    option.textContent = district.name;
                                    option.dataset.id = district.id;
                                    districtSelect.appendChild(option);
                                });
                                districtSelect.disabled = false;
                            }
                        });
                }
            });

            // Xử lý phường/xã
            document.getElementById('district').addEventListener('change', function() {
                const districtId = this.options[this.selectedIndex].dataset.id;
                const wardSelect = document.getElementById('ward');

                // Reset phường/xã
                wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                wardSelect.disabled = true;

                if (districtId) {
                    fetch(`${apiBaseUrl}/districts/${districtId}/wards`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                data.data.forEach(ward => {
                                    const option = document.createElement('option');
                                    option.value = ward.name;
                                    option.textContent = ward.name;
                                    wardSelect.appendChild(option);
                                });
                                wardSelect.disabled = false;
                            }
                        });
                }
            });

            // Reset trạng thái màn hình khi load trang
            document.addEventListener('DOMContentLoaded', function() {
                // Đảm bảo không có backdrop nào tồn tại khi trang load
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });

            // Xử lý đóng modal và reset backdrop
            function resetModalState() {
                // Xóa tất cả backdrop của modal
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());

                // Reset trạng thái body
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }

            // Xử lý nút đóng modal
            document.querySelector('.btn-close').addEventListener('click', function() {
                resetModalState();
            });

            // Xử lý nút Đóng trong modal footer
            document.querySelector('.modal-footer .btn-secondary').addEventListener('click', function() {
                resetModalState();
            });

            // Xử lý submit form
            document.getElementById('submit-registration').addEventListener('click', async function(e) {
                e.preventDefault();

                const form = document.getElementById('registration-form');

                // Validate form
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                // Disable nút submit
                const submitBtn = this;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...';

                try {
                    // Thu thập dữ liệu form
                    const formData = new FormData(form);
                    const formDataObj = Object.fromEntries(formData.entries());

                    // Xử lý giá trị từ select multiple
                    const usedProductsSelect = document.getElementById('used_products');
                    const selectedOptions = Array.from(usedProductsSelect.selectedOptions).map(option => option.value);

                    // Nếu có "Chưa sử dụng" và các option khác, chỉ giữ lại các option khác
                    const unusedIndex = selectedOptions.indexOf('Chưa sử dụng');
                    if (unusedIndex !== -1 && selectedOptions.length > 1) {
                        selectedOptions.splice(unusedIndex, 1);
                    }

                    // Gán giá trị cho used_products
                    formDataObj.used_products = selectedOptions.length > 0 ? selectedOptions.join(', ') : 'Chưa sử dụng';

                    // Debug
                    console.log('Form Data:', formDataObj);
                    console.log('Used Products:', formDataObj.used_products);

                    // Gửi request
                    const response = await fetch(registerUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(formDataObj)
                    });

                    const data = await response.json();
                    console.log('Server Response:', data);

                    if (response.ok && data.success) {
                        // Lưu ID người tham gia
                        participantId = data.participant_id;
                        isRegistered = true;

                        // Đóng modal
                        resetModalState();

                        // Hiển thị thông báo thành công
                        Swal.fire({
                            icon: 'success',
                            title: 'Đăng ký thành công!',
                            text: 'Bạn có thể bắt đầu quay thưởng ngay.',
                            confirmButtonColor: '#28a745'
                        });

                        // Enable nút quay
                        document.getElementById('spin-button').disabled = false;
                    } else {
                        throw new Error(data.message || 'Có lỗi xảy ra khi đăng ký');
                    }
                } catch (error) {
                    console.error('Registration Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Đăng ký thất bại!',
                        text: error.message,
                        confirmButtonColor: '#dc3545'
                    });
                } finally {
                    // Reset nút submit
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> ĐĂNG KÝ';
                }
            });

            // Xử lý select multiple cho used_products
            document.getElementById('used_products').addEventListener('change', function() {
                const selectedOptions = Array.from(this.selectedOptions).map(option => option.value);
                const unusedIndex = selectedOptions.indexOf('Chưa sử dụng');

                // Nếu chọn "Chưa sử dụng" và có các option khác được chọn
                if (unusedIndex !== -1 && selectedOptions.length > 1) {
                    // Nếu mới chọn "Chưa sử dụng", bỏ chọn các option khác
                    if (this.options[0].selected) {
                        for (let i = 1; i < this.options.length; i++) {
                            this.options[i].selected = false;
                        }
                    } else {
                        // Nếu chọn option khác, bỏ chọn "Chưa sử dụng"
                        this.options[0].selected = false;
                    }
                }
            });
        });
    </script>
</body>
</html>
