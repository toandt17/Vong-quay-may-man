$(document).ready(function() {
    // Biến lưu thông tin người tham gia
    let participantId = null;
    let isRegistered = false;
    let prizes = [];
    let debugMode = wheelConfig.debug || false; // Sử dụng cấu hình từ index.blade.php
    let segmentMap = {}; // Map các phân đoạn với ID giải
    let isSpinning = false; // Trạng thái đang quay
    let prizeIdToIndexMap = {}; // Map ID giải thưởng với vị trí trên vòng quay
    let registerModal = null; // Biến lưu trữ đối tượng Modal

    // Lấy danh sách giải thưởng từ server
    function initializeWheel() {
        prizes = wheelData.prizes || [];

        // Sắp xếp lại giải thưởng để đảm bảo thứ tự nhất quán
        prizes.sort((a, b) => parseInt(a.id) - parseInt(b.id));

        // Kiểm tra dữ liệu giải thưởng
        if (debugMode) {
            console.log("Danh sách giải thưởng:", prizes);
        }

        // Tạo vòng quay sau khi lấy giải thưởng
        createWheel();
    }
    function getAlternatingColor(index) {
        if (index % 2 === 0) {
            return { background: '#FFFFFF', text: '#000000' }; // Màu trắng với chữ đen
        } else {
            return { background: '#FFFFFF', text: '#ffffff' }; // Màu đỏ với chữ đen
        }
    }
// Tạo vòng quay
const createWheel = () => {
    const wheel = document.getElementById('wheel');
    if (!wheel || !prizes.length) return;

    // Đảm bảo số lượng phân đoạn cố định để chia đều
    const totalPrizes = prizes.length;
    const anglePerSegment = 360 / totalPrizes;

    // Xóa tất cả các phân đoạn hiện có
    wheel.innerHTML = '';

    // Tính toán kích thước text và hình ảnh dựa trên kích thước màn hình
    const isMobile = window.innerWidth <= 768;
    const textDistance = isMobile ? 30 : 35; // Giảm khoảng cách text trên mobile
    const imgDistance = isMobile ? 65 : 70; // Giảm khoảng cách hình ảnh trên mobile
    const fontSize = isMobile ? '10px' : '14px'; // Giảm kích thước font trên mobile

    // Tạo các phân đoạn với kích thước bằng nhau
    for (let i = 0; i < totalPrizes; i++) {
        const prize = prizes[i];

        // Lưu thông tin mapping giữa ID giải và vị trí trên vòng quay
        segmentMap[prize.id] = i;
        prizeIdToIndexMap[prize.id] = i;

        const startAngle = i * anglePerSegment;
        const endAngle = (i + 1) * anglePerSegment;

        // Tính toán tọa độ của phân đoạn
        const startRad = (startAngle - 90) * Math.PI / 180;
        const endRad = (endAngle - 90) * Math.PI / 180;
        const centerX = 50;
        const centerY = 50;
        const radius = 50;

        const x1 = centerX + radius * Math.cos(startRad);
        const y1 = centerY + radius * Math.sin(startRad);
        const x2 = centerX + radius * Math.cos(endRad);
        const y2 = centerY + radius * Math.sin(endRad);

        // Tạo SVG cho phân đoạn
        const segment = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        segment.setAttribute('viewBox', '0 0 100 100');
        segment.style.position = 'absolute';
        segment.style.width = '100%';
        segment.style.height = '100%';
        segment.style.transform = `rotate(${startAngle}deg)`;
        segment.style.transformOrigin = 'center';
        segment.classList.add('wheel-segment');

        // Thêm data attributes cho debug
        segment.dataset.prizeId = prize.id;
        segment.dataset.prizeIndex = i;
        segment.dataset.prizeName = prize.name;
        segment.dataset.segmentStart = startAngle;
        segment.dataset.segmentEnd = endAngle;

        // Lấy màu sắc xen kẽ
        const colors = getAlternatingColor(i);

        // Tạo đường path cho phân đoạn
        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        path.setAttribute('d', `M${centerX},${centerY} L${x1},${y1} A${radius},${radius} 0 0,1 ${x2},${y2} Z`);

        // Gán màu cho phân đoạn
        path.setAttribute('fill', colors.background);

        path.setAttribute('stroke', '#ffffff');
        path.setAttribute('stroke-width', '0.5');

        // Thêm path vào segment
        segment.appendChild(path);

        // Thêm segment vào wheel
        wheel.appendChild(segment);

        // Thêm text giải thưởng
        const midAngle = startAngle + anglePerSegment / 2;
        const midRad = (midAngle - 90) * Math.PI / 180;
        const textX = 50 + textDistance * Math.cos(midRad);
        const textY = 50 + textDistance * Math.sin(midRad);

        const text = document.createElement('div');
        text.className = 'wheel-segment-text';
        text.textContent = prize.name;
        text.style.left = textX + '%';
        text.style.top = textY + '%';
        text.style.transform = `translate(-50%, -50%) rotate(${midAngle + 90}deg)`;
        text.style.color = colors.text;
        text.style.fontWeight = 'bold';
        text.style.fontSize = fontSize;

        wheel.appendChild(text);

        // Điều chỉnh kích thước và vị trí hình ảnh
        if (prize.image) {
            const imgX = 50 + imgDistance * Math.cos(midRad);
            const imgY = 50 + imgDistance * Math.sin(midRad);

            const imgContainer = document.createElement('div');
            imgContainer.className = 'wheel-image';
            imgContainer.style.left = imgX + '%';
            imgContainer.style.top = imgY + '%';
            imgContainer.style.transform = `translate(-50%, -50%) rotate(${midAngle + 90}deg)`;
            imgContainer.style.width = isMobile ? '35px' : '45px';
            imgContainer.style.height = isMobile ? '35px' : '45px';

            const img = document.createElement('img');
            img.src = prize.image;
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'contain';
            img.style.filter = 'drop-shadow(1px 1px 2px rgba(0,0,0,0.5))';

            imgContainer.appendChild(img);
            wheel.appendChild(imgContainer);
        }
    }

    if (debugMode) {
        console.log('Vòng quay đã được tạo với ' + totalPrizes + ' giải thưởng');
        console.log('Mapping giữa ID và vị trí:', prizeIdToIndexMap);
    }
};

// Thêm event listener cho resize để cập nhật kích thước
window.addEventListener('resize', () => {
    createWheel();
});

// ...existing code...

    // Hàm tìm vị trí phân đoạn dựa vào ID giải thưởng
    function findSegmentByPrizeId(prizeId) {
        // Chuyển đổi prizeId thành số nguyên để so sánh chính xác
        const targetPrizeId = parseInt(prizeId);

        // Kiểm tra trong map trước (cách nhanh nhất)
        if (prizeIdToIndexMap.hasOwnProperty(targetPrizeId)) {
            return prizeIdToIndexMap[targetPrizeId];
        }

        // Tìm trong mảng prizes
        const index = prizes.findIndex(p => parseInt(p.id) === targetPrizeId);
        if (index !== -1) {
            // Cập nhật map để lần sau tìm nhanh hơn
            prizeIdToIndexMap[targetPrizeId] = index;
            return index;
        }

        // Không tìm thấy, trả về -1
        console.error("Không tìm thấy giải thưởng ID:", targetPrizeId);

        // Nếu không tìm thấy nhưng cần đảm bảo chính xác, trả về phân đoạn đầu tiên
        if (wheelConfig.ensureExactPointer && prizes.length > 0) {
            console.warn("Sử dụng phân đoạn đầu tiên thay thế");
            return 0;
        }

        return -1;
    }

    // Hiển thị kết quả trúng thưởng
    function showPrizeResult(prize, isWin) {
        // Khởi tạo modal kết quả nếu chưa có
        const resultModal = new bootstrap.Modal(document.getElementById('resultModal'));

        // Xác nhận kết quả quay
        const isCorrectResult = verifySpinResult(prize);

        if (debugMode) {
            console.log("Kết quả quay:", isWin ? "Trúng thưởng" : "Không trúng");
            console.log("Giải thưởng từ server:", prize);
            console.log("Kết quả chính xác:", isCorrectResult);
        }

        // Nếu kết quả không chính xác và cần đảm bảo chính xác 100%
        if (!isCorrectResult && wheelConfig.ensureExactPointer && isWin && prize) {
            console.warn("Phát hiện kết quả không chính xác, điều chỉnh lại vị trí vòng quay");

            // Lấy vị trí phân đoạn chính xác
            const correctSegment = findSegmentByPrizeId(prize.id);
            if (correctSegment !== -1) {
                // Tính toán góc chính xác
                const totalPrizes = prizes.length;
                const anglePerSegment = 360 / totalPrizes;
                const correctAngle = 360 - (correctSegment * anglePerSegment) - (anglePerSegment / 2);

                // Điều chỉnh vị trí vòng quay ngay lập tức
                const wheel = document.getElementById('wheel');
                if (wheel) {
                    wheel.style.transition = 'transform 0.3s ease-out';
                    wheel.style.transform = `rotate(${correctAngle}deg)`;
                    wheel.dataset.currentRotation = correctAngle;

                    if (debugMode) {
                        console.log("Điều chỉnh vòng quay đến góc:", correctAngle);
                    }
                }
            }
        }

        if (isWin && prize) {
            // Trúng thưởng
            $('#resultModal').addClass('win').removeClass('lose');
            $('#result-modal-header').css('background', 'linear-gradient(135deg, var(--lucky-gold), var(--festival-red))');
            $('#modal-result-title').text('Cảm ơn bạn đã tham gia!');
            $('#modal-result-message').text(`${prize.name}!`);

            // Hiển thị thông tin chi tiết giải thưởng
            $('#modal-prize-name').text(prize.name);
            $('#modal-prize-description').text(prize.description || '');

            // Hiển thị hình ảnh nếu có
            if (prize.image) {
                $('#modal-prize-image').attr('src', prize.image);
                $('#modal-prize-image').show();
            } else {
                $('#modal-prize-image').hide();
            }

            // Hiển thị số lượng nếu có
            // if (prize.quantity) {
            //     $('#modal-prize-quantity').text(`Còn lại: ${prize.remaining}/${prize.quantity}`);
            // } else {
            //     $('#modal-prize-quantity').text('');
            // }

            $('#modal-prize-details').show();

            // Hiển thị modal kết quả
            resultModal.show();

            // Hiệu ứng pháo hoa khi trúng thưởng
            setTimeout(function() {
                startConfetti();
            }, 300);
        } else {
            // Không trúng thưởng
            $('#resultModal').addClass('lose').removeClass('win');
            $('#result-modal-header').css('background', 'linear-gradient(135deg, #6c757d, #343a40)');
            $('#modal-result-title').text('Rất tiếc!');
            $('#modal-result-message').text('Bạn không trúng giải lần này. Chúc may mắn lần sau!');
            $('#modal-prize-details').hide();

            // Hiển thị modal kết quả
            resultModal.show();
        }

        // Vẫn hiển thị kết quả trực tiếp trên trang (có thể giữ lại hoặc bỏ)
        if (isWin && prize) {
            $('#result-container').removeClass('lose-result').addClass('win-result');
            $('#result-title').text('Cảm ơn bạn đã tham gia!');
            $('#result-message').text(`${prize.name}!`);
            $('#prize-name').text(prize.name);
            $('#prize-description').text(prize.description || '');
            if (prize.image) {
                $('#prize-image').attr('src', prize.image);
                $('#prize-image').show();
            } else {
                $('#prize-image').hide();
            }
            $('#prize-details').show();
        } else {
            $('#result-container').removeClass('win-result').addClass('lose-result');
            $('#result-title').text('Rất tiếc!');
            $('#result-message').text('Bạn không trúng giải lần này. Chúc may mắn lần sau!');
            $('#prize-details').hide();
        }
        $('#result-container').fadeIn().addClass('show');
    }

    // Hàm tạo hiệu ứng pháo hoa
    function startConfetti() {
        // Sử dụng thư viện canvas-confetti
        confetti({
            particleCount: 150,
            spread: 70,
            origin: { y: 0.6 },
            colors: ['#FFD700', '#FF0000', '#00FF00', '#0000FF', '#FF00FF']
        });

        // Tạo thêm pháo hoa ở nhiều vị trí
        setTimeout(() => {
            confetti({
                particleCount: 100,
                angle: 60,
                spread: 55,
                origin: { x: 0 },
                colors: ['#FFD700', '#FF0000', '#00FF00']
            });
        }, 250);

        setTimeout(() => {
            confetti({
                particleCount: 100,
                angle: 120,
                spread: 55,
                origin: { x: 1 },
                colors: ['#0000FF', '#FF00FF', '#FFD700']
            });
        }, 400);

        // Pháo hoa liên tục trong 5 giây
        const duration = 5 * 1000;
        const end = Date.now() + duration;

        (function frame() {
            confetti({
                particleCount: 2,
                angle: 60,
                spread: 55,
                origin: { x: 0 },
                colors: ['#FFD700']
            });

            confetti({
                particleCount: 2,
                angle: 120,
                spread: 55,
                origin: { x: 1 },
                colors: ['#FF0000']
            });

            if (Date.now() < end) {
                requestAnimationFrame(frame);
            }
        }());
    }

    // Khởi tạo modal
    function initializeModal() {
        // Khởi tạo modal Bootstrap
        registerModal = new bootstrap.Modal(document.getElementById('registerModal'), {
            backdrop: 'static', // Không đóng khi click bên ngoài
            keyboard: false // Không đóng khi nhấn ESC
        });

        // Sự kiện khi nút "Bắt đầu ngay" được click
        $('#open-register-modal').click(function() {
            registerModal.show();
        });

        // Sự kiện khi nút "Đăng ký" trong modal được click
        $('#submit-registration').click(function() {
            submitRegistrationForm();
        });

        // Bắt sự kiện nhấn Enter trong form
        $('#registration-form input, #registration-form select').keypress(function(e) {
            if (e.which === 13) {
                e.preventDefault();
                submitRegistrationForm();
            }
        });

        // Hiển thị/ẩn trường giống lúa khi chọn là nông dân
        $('#is_farmer').change(function() {
            if($(this).is(':checked')) {
                $('.farmer-fields').show();
                $('#rice_variety, #rice_stage').prop('required', true);
                // Kích hoạt kiểm tra xác thực cho các trường của nông dân
                validateInput($('#rice_variety'), 'Vui lòng chọn giống lúa');
                validateInput($('#rice_stage'), 'Vui lòng chọn giai đoạn sinh trưởng');
            } else {
                $('.farmer-fields').hide();
                $('#rice_variety, #rice_stage').prop('required', false).val('');
                // Xóa trạng thái lỗi và thông báo
                $('#rice_variety').removeClass('is-invalid').removeClass('is-valid');
                $('#rice_stage').removeClass('is-invalid').removeClass('is-valid');
                // Reset used_products
                $('input[name="used_products[]"]').prop('checked', false);
                $('#product-none').prop('checked', true);
            }
        });

        // Kiểm tra các trường của nông dân khi thay đổi
        $('#rice_variety, #rice_stage').on('change blur', function() {
            if ($('#is_farmer').is(':checked')) {
                const id = $(this).attr('id');
                let errorMsg = '';
                if (id === 'rice_variety') {
                    errorMsg = 'Vui lòng chọn giống lúa';
                } else if (id === 'rice_stage') {
                    errorMsg = 'Vui lòng chọn giai đoạn sinh trưởng';
                }
                validateInput($(this), errorMsg);
            }
        });

        // Xử lý checkbox cho used_products
        $('input[name="used_products[]"]').on('change', function() {
            const $productNone = $('#product-none');

            // Nếu đây là checkbox "Chưa sử dụng"
            if (this.id === 'product-none') {
                if (this.checked) {
                    // Nếu "Chưa sử dụng" được chọn, bỏ chọn tất cả các checkbox khác
                    $('input[name="used_products[]"]').not(this).prop('checked', false);
                }
            } else {
                // Nếu đây là một sản phẩm khác và được chọn
                if (this.checked) {
                    // Bỏ chọn checkbox "Chưa sử dụng"
                    $productNone.prop('checked', false);
                } else {
                    // Nếu không còn sản phẩm nào được chọn, chọn "Chưa sử dụng"
                    if ($('input[name="used_products[]"]:checked').not($productNone).length === 0) {
                        $productNone.prop('checked', true);
                    }
                }
            }
        });

        // Kiểm tra tên khi người dùng nhập
        $('#name').on('input blur', function() {
            validateName($(this));
        });

        // Kiểm tra số điện thoại khi người dùng nhập
        $('#phone').on('input blur', function() {
            validatePhone($(this));
        });

        // Kiểm tra các trường select khi thay đổi
        $('#province, #district, #ward').on('change blur', function() {
            validateSelect($(this));
        });

        // Kiểm tra địa chỉ khi người dùng nhập
        $('#address').on('input blur', function() {
            validateInput($(this), 'Vui lòng nhập địa chỉ cụ thể');
        });

        // Thêm trình nghe sự kiện cho trường số điện thoại
        $('#phone').on('input', function() {
            // Xóa highlight lỗi khi người dùng bắt đầu nhập
            $(this)
                .removeClass('is-invalid shake-error phone-error-animation')
                .css({
                    'border-color': '',
                    'border-width': '',
                    'background-color': ''
                });

            // Ẩn thông báo lỗi
            $('#phone-error')
                .text('')
                .css({
                    'display': 'none'
                });
        });
    }

    // Hàm kiểm tra tên
    function validateName(field) {
        const value = field.val().trim();
        const error = $('#name-error');

        if (!value) {
            field.addClass('is-invalid');
            error.text('Họ tên không được để trống');
            return false;
        } else {
            field.removeClass('is-invalid').addClass('is-valid');
            error.text('');
            return true;
        }
    }

    // Hàm kiểm tra số điện thoại
    function validatePhone(field) {
        const value = field.val().trim();
        const error = $('#phone-error');
        const phoneRegex = /^[0-9]{10,11}$/;

        if (!value) {
            field.addClass('is-invalid');
            error.text('Số điện thoại không được để trống');
            return false;
        } else if (!/^[0-9]+$/.test(value)) {
            field.addClass('is-invalid');
            error.text('Số điện thoại chỉ được chứa chữ số');
            return false;
        } else if (!phoneRegex.test(value)) {
            field.addClass('is-invalid');
            error.text('Số điện thoại phải có 10 đến 11 chữ số');
            return false;
        } else {
            field.removeClass('is-invalid').addClass('is-valid');
            error.text('');
            return true;
        }
    }

    // Hàm kiểm tra các select
    function validateSelect(field) {
        const value = field.val();
        const id = field.attr('id');
        const error = $(`#${id}-error`);

        if (!value) {
            field.addClass('is-invalid');
            return false;
        } else {
            field.removeClass('is-invalid').addClass('is-valid');
            error.text('');
            return true;
        }
    }

    // Hàm kiểm tra input chung
    function validateInput(field, errorMsg) {
        const value = field.val().trim();
        const id = field.attr('id');
        const error = $(`#${id}-error`);

        // Không validate trường nông dân nếu không phải là nông dân
        if ((id === 'rice_variety' || id === 'rice_stage') && !$('#is_farmer').is(':checked')) {
            return true;
        }

        if (!value) {
            field.addClass('is-invalid');
            error.text(errorMsg);
            return false;
        } else {
            field.removeClass('is-invalid').addClass('is-valid');
            error.text('');
            return true;
        }
    }

    // Hàm submit form đăng ký từ modal
    function submitRegistrationForm() {
        // Kiểm tra validate form
        let isValid = true;

        // Validate các trường cơ bản
        isValid = validateName($('#name')) && isValid;
        isValid = validatePhone($('#phone')) && isValid;
        isValid = validateSelect($('#province')) && isValid;
        isValid = validateSelect($('#district')) && isValid;
        isValid = validateSelect($('#ward')) && isValid;
        isValid = validateInput($('#address'), 'Vui lòng nhập địa chỉ cụ thể') && isValid;

        // Nếu là nông dân thì validate thêm các trường
        if ($('#is_farmer').is(':checked')) {
            isValid = validateInput($('#rice_variety'), 'Vui lòng chọn giống lúa') && isValid;
            isValid = validateInput($('#rice_stage'), 'Vui lòng chọn giai đoạn sinh trưởng') && isValid;
        } else {
            // Bỏ trạng thái lỗi nếu không phải nông dân
            $('#rice_variety').removeClass('is-invalid').removeClass('is-valid');
            $('#rice_stage').removeClass('is-invalid').removeClass('is-valid');
        }

        if (!isValid) {
            return;
        }

        // Disable nút submit
        $('#submit-registration').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...');

        // Lấy dữ liệu form
        const form = document.getElementById('registration-form');
        const formData = new FormData(form);

        // Thu thập dữ liệu
        const formDataObj = {};
        for (const [key, value] of formData.entries()) {
            // Xử lý đặc biệt cho trường hợp mảng
            if (key.endsWith('[]')) {
                const baseKey = key.slice(0, -2);
                if (!formDataObj[baseKey]) {
                    formDataObj[baseKey] = [];
                }
                formDataObj[baseKey].push(value);
            } else {
                formDataObj[key] = value;
            }
        }

        // Xử lý sản phẩm đã sử dụng
        const selectedProducts = $('input[name="used_products[]"]:checked').map(function() {
            return this.value;
        }).get();

        // Chuyển đổi mảng sản phẩm thành chuỗi được phân tách bằng dấu phẩy
        formDataObj.used_products = selectedProducts.join(', ');

        // Gửi request đăng ký
        $.ajax({
            url: registerUrl,
            type: 'POST',
            data: formDataObj,
            beforeSend: function() {
                // Hiển thị loading overlay
                $('body').append('<div id="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; justify-content: center; align-items: center;"><div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status"><span class="visually-hidden">Đang xử lý...</span></div></div>');
            },
            success: function(response) {
                // Xóa loading overlay
                $('#loading-overlay').remove();

                if (response.success) {
                    // Đóng modal đăng ký
                    registerModal.hide();

                    // Tùy chỉnh thông báo dựa trên response
                    let icon = 'success';
                    let title = 'Đăng ký thành công!';

                    // Nếu số điện thoại đã tồn tại, thông báo đặc biệt
                    if (response.is_existed) {
                        icon = 'info';
                        title = 'Số điện thoại đã tồn tại!';
                    }

                    Swal.fire({
                        icon: icon,
                        title: title,
                        text: response.message,
                        confirmButtonColor: '#28a745'
                    });

                    // Lưu ID người tham gia
                    participantId = response.participant_id;
                    isRegistered = true;

                    // Lưu kết quả đã xác định trước
                    if (response.pre_determined_result) {
                        window.preDeterminedResult = response.pre_determined_result;

                        if (debugMode) {
                            console.log("Kết quả quay ngầm:", window.preDeterminedResult);
                        }
                    }

                    // Enable nút quay
                    $('#spin-button').prop('disabled', false);

                    // Hiển thị thông báo hướng dẫn
                    showWheelInstructions();

                    // Animate wheel section
                    if (typeof window.animateWheelAfterRegistration === 'function') {
                        window.animateWheelAfterRegistration();
                    }
                }
            },
            error: function(xhr) {
                // Xóa loading overlay
                $('#loading-overlay').remove();

                let errorMessage = 'Đã xảy ra lỗi khi đăng ký.';
                let isPhoneUsed = false;

                // Kiểm tra xem có phải lỗi số điện thoại đã được sử dụng không
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.error_type === 'phone_used') {
                        isPhoneUsed = true;
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;

                        // Fallback check for phone already used
                        if (errorMessage.toLowerCase().includes('số điện thoại') && errorMessage.toLowerCase().includes('đã được sử dụng')) {
                            isPhoneUsed = true;
                        }
                    } else if (xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors)[0][0];
                    }
                }

                if (isPhoneUsed) {
                    // Highlight trường điện thoại với màu nhẹ hơn
                    $('#phone')
                        .removeClass('is-valid')
                        .addClass('is-invalid shake-error phone-error-animation')
                        .css({
                            'border-color': '#dc3545',
                            'border-width': '1px',
                            'background-color': 'rgba(220, 53, 69, 0.03)'
                        });

                    // Hiển thị thông báo lỗi bên dưới trường điện thoại
                    $('#phone-error')
                        .text('Số điện thoại này đã được sử dụng.')
                        .css({
                            'color': '#dc3545',
                            'font-weight': 'normal',
                            'display': 'block',
                            'font-size': '12px'
                        });

                    // Hiển thị modal lỗi số điện thoại (nhỏ và đơn giản)
                    const phoneErrorModal = new bootstrap.Modal(document.getElementById('phoneErrorModal'));
                    phoneErrorModal.show();

                    // Focus vào trường điện thoại sau khi đóng modal
                    $('#phoneErrorModal').on('hidden.bs.modal', function() {
                        setTimeout(function() {
                            $('#phone').focus().select();
                        }, 100);
                    });

                } else {
                    // Hiển thị thông báo lỗi dựa trên loại lỗi
                    Swal.fire({
                        icon: 'error',
                        title: 'Đăng ký thất bại!',
                        text: errorMessage,
                        confirmButtonColor: '#dc3545'
                    });
                }

                // Re-enable nút submit
                $('#submit-registration').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i> ĐĂNG KÝ');
            }
        });
    }

    // Hiển thị hướng dẫn khi đăng ký thành công
    function showWheelInstructions() {
        Swal.fire({
            title: 'Bắt đầu quay thưởng!',
            html: 'Nhấn nút <b>QUAY NGAY!</b> để bắt đầu quay vòng quay may mắn.',
            icon: 'info',
            confirmButtonText: 'Đã hiểu',
            confirmButtonColor: '#28a745'
        });

        // Thêm hiệu ứng nhấp nháy cho nút quay
        $('#spin-button').addClass('animate__animated animate__pulse animate__infinite');
    }

    // Xử lý quay vòng quay
    $('#spin-button').click(function() {
        if (!isRegistered || !participantId) {
            Swal.fire({
                icon: 'warning',
                title: 'Chưa đăng ký!',
                text: 'Vui lòng đăng ký thông tin trước khi quay thưởng.',
                confirmButtonColor: '#ffc107'
            });
            return;
        }

        // Kiểm tra nếu đang quay
        if (isSpinning) {
            return;
        }

        // Đánh dấu đang quay
        isSpinning = true;

        // Disable nút quay
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang quay...');

        // Ẩn kết quả cũ nếu có
        $('#result-container').hide();
        $('#result-container').removeClass('show winner-glow');

        // Tạo effect quay nhẹ trong khi chờ kết quả từ server
        const wheel = document.getElementById('wheel');
        const initialRotation = parseFloat(wheel.style.transform.replace('rotate(', '').replace('deg)', '') || 0);
        let tempRotation = initialRotation;
        let tempRotationInterval = setInterval(() => {
            tempRotation += 5;
            wheel.style.transform = `rotate(${tempRotation}deg)`;
        }, 50);

        // Sử dụng kết quả đã xác định trước
        if (window.preDeterminedResult) {
            // Dừng hiệu ứng quay tạm thời
            clearInterval(tempRotationInterval);

            const predeterminedData = window.preDeterminedResult;
            const isWin = predeterminedData.is_win;
            const prize = predeterminedData.prize;

            // Debug thông tin
            if (debugMode) {
                console.log("Kết quả xác định trước:", predeterminedData);
                console.log("Trúng thưởng:", isWin);
                console.log("Giải thưởng:", prize);
            }

            // Nếu trúng thưởng và có thông tin giải
            if (isWin && prize) {
                // Tìm vị trí phân đoạn tương ứng với giải thưởng
                const prizeSegmentIndex = findSegmentByPrizeId(prize.id);

                if (prizeSegmentIndex !== -1) {
                    // QUAN TRỌNG: Hiệu chỉnh góc quay để đảm bảo mũi tên trỏ đúng vào giải thưởng
                    // Phân tích: mũi tên nằm ở góc 0 độ (trên cùng), và vòng quay quay ngược chiều kim đồng hồ
                    const totalPrizes = prizes.length;
                    const anglePerSegment = 360 / totalPrizes;

                    // Góc cơ bản để mũi tên trỏ vào giữa phân đoạn
                    // Công thức: 360 - (index * góc mỗi phân đoạn) - (góc mỗi phân đoạn / 2)
                    // Trừ đi góc mỗi phân đoạn / 2 để trỏ vào giữa phân đoạn
                    let targetAngle = 360 - (prizeSegmentIndex * anglePerSegment) - (anglePerSegment / 2);

                    // Đảm bảo targetAngle nằm trong khoảng [0, 360)
                    targetAngle = targetAngle % 360;
                    if (targetAngle < 0) targetAngle += 360;

                    // Bù trừ góc cho mũi tên
                    // Sử dụng offset multiplier để điều chỉnh vị trí chính xác
                    const offsetMultiplier = wheelConfig.angleOffset || 0;
                    // Tính góc offset dựa trên số phân đoạn cần dịch chuyển
                    const offsetAngle = anglePerSegment * offsetMultiplier;
                    // Góc cuối cùng sau khi điều chỉnh
                    const adjustedAngle = targetAngle + offsetAngle;

                    // Đảm bảo adjustedAngle nằm trong khoảng [0, 360)
                    const finalTargetAngle = adjustedAngle % 360;

                    if (debugMode) {
                        console.log(`Phân đoạn mục tiêu: ${prizeSegmentIndex} (ID: ${prize.id})`);
                        console.log(`Góc cơ bản: ${targetAngle}°`);
                        console.log(`Điều chỉnh góc: ${offsetAngle}° (${offsetMultiplier} phân đoạn)`);
                        console.log(`Góc cuối cùng: ${finalTargetAngle}°`);
                    }

                    // Số vòng quay cố định + góc cuối cùng
                    const rotations = wheelConfig.spinEffect?.rotations || 5;
                    const totalRotation = (rotations * 360) + finalTargetAngle;
                    const duration = wheelConfig.spinEffect?.duration || 5000;

                    if (debugMode) {
                        console.log("Số phân đoạn:", totalPrizes);
                        console.log("Góc mỗi phân đoạn:", anglePerSegment);
                        console.log("Tổng góc quay:", totalRotation);
                    }

                    // Bắt đầu animation quay
                    spinToPosition(wheel, tempRotation, totalRotation, duration, function() {
                        // Kiểm tra xem vị trí hiện tại có chính xác không
                        const isPositionCorrect = verifySpinResult(prize);

                        if (isPositionCorrect) {
                            // Hiển thị kết quả khi quay xong và vị trí chính xác
                            showPrizeResult(prize, true);
                            isSpinning = false;

                            // Disable nút quay vĩnh viễn (mỗi người chỉ được quay 1 lần)
                            $('#spin-button').prop('disabled', true).text('Đã sử dụng lượt quay');
                        } else {
                            // Nếu vị trí không chính xác, điều chỉnh
                            adjustToCorrectPosition(prize, function() {
                                showPrizeResult(prize, true);
                                isSpinning = false;
                                $('#spin-button').prop('disabled', true).text('Đã sử dụng lượt quay');
                            });
                        }
                    });
                } else {
                    console.error("Không tìm thấy phân đoạn cho giải thưởng:", prize.id);
                    isSpinning = false;
                    $('#spin-button').prop('disabled', false).text('QUAY NGAY!');
                }
            } else {
                // Nếu không trúng thưởng, quay đến một vị trí ngẫu nhiên (không phải vị trí của giải thưởng)
                const totalPrizes = prizes.length;
                const anglePerSegment = 360 / totalPrizes;

                // Tạo một góc ngẫu nhiên
                const randomAngle = Math.floor(Math.random() * 360);

                // Số vòng quay cố định + góc ngẫu nhiên
                const rotations = wheelConfig.spinEffect?.rotations || 5;
                const totalRotation = (rotations * 360) + randomAngle;
                const duration = wheelConfig.spinEffect?.duration || 5000;

                // Bắt đầu animation quay
                spinToPosition(wheel, tempRotation, totalRotation, duration, function() {
                    // Hiển thị kết quả không trúng thưởng
                    showPrizeResult(null, false);
                    isSpinning = false;

                    // Disable nút quay vĩnh viễn (mỗi người chỉ được quay 1 lần)
                    $('#spin-button').prop('disabled', true).text('Đã sử dụng lượt quay');
                });
            }
        } else {
            // Nếu không có kết quả xác định trước, thực hiện gọi API để lấy kết quả
            // Gửi request quay đến server
            $.ajax({
                url: spinUrl,
                type: 'POST',
                data: {
                    participant_id: participantId,
                    lucky_wheel_id: luckyWheelId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Dừng hiệu ứng quay tạm thời
                    clearInterval(tempRotationInterval);

                    if (response.success) {
                        // Lấy thông tin về giải thưởng từ server
                        const isWin = response.is_win;
                        const prize = response.prize;

                        // Debug thông tin
                        if (debugMode) {
                            console.log("Server trả về:", response);
                            console.log("Trúng thưởng:", isWin);
                            console.log("Giải thưởng:", prize);
                        }

                        // Nếu trúng thưởng và có thông tin giải
                        if (isWin && prize) {
                            // Tìm vị trí phân đoạn tương ứng với giải thưởng
                            const prizeSegmentIndex = findSegmentByPrizeId(prize.id);

                            if (prizeSegmentIndex !== -1) {
                                // QUAN TRỌNG: Hiệu chỉnh góc quay để đảm bảo mũi tên trỏ đúng vào giải thưởng
                                // Phân tích: mũi tên nằm ở góc 0 độ (trên cùng), và vòng quay quay ngược chiều kim đồng hồ
                                const totalPrizes = prizes.length;
                                const anglePerSegment = 360 / totalPrizes;

                                // Góc cơ bản để mũi tên trỏ vào giữa phân đoạn
                                // Công thức: 360 - (index * góc mỗi phân đoạn) - (góc mỗi phân đoạn / 2)
                                // Trừ đi góc mỗi phân đoạn / 2 để trỏ vào giữa phân đoạn
                                let targetAngle = 360 - (prizeSegmentIndex * anglePerSegment) - (anglePerSegment / 2);

                                // Đảm bảo targetAngle nằm trong khoảng [0, 360)
                                targetAngle = targetAngle % 360;
                                if (targetAngle < 0) targetAngle += 360;

                                // Bù trừ góc cho mũi tên
                                // Sử dụng offset multiplier để điều chỉnh vị trí chính xác
                                const offsetMultiplier = wheelConfig.angleOffset || 0;
                                // Tính góc offset dựa trên số phân đoạn cần dịch chuyển
                                const offsetAngle = anglePerSegment * offsetMultiplier;
                                // Góc cuối cùng sau khi điều chỉnh
                                const adjustedAngle = targetAngle + offsetAngle;

                                // Đảm bảo adjustedAngle nằm trong khoảng [0, 360)
                                const finalTargetAngle = adjustedAngle % 360;

                                if (debugMode) {
                                    console.log(`Phân đoạn mục tiêu: ${prizeSegmentIndex} (ID: ${prize.id})`);
                                    console.log(`Góc cơ bản: ${targetAngle}°`);
                                    console.log(`Điều chỉnh góc: ${offsetAngle}° (${offsetMultiplier} phân đoạn)`);
                                    console.log(`Góc cuối cùng: ${finalTargetAngle}°`);
                                }

                                // Số vòng quay cố định + góc cuối cùng
                                const rotations = wheelConfig.spinEffect?.rotations || 5;
                                const totalRotation = (rotations * 360) + finalTargetAngle;
                                const duration = wheelConfig.spinEffect?.duration || 5000;

                                if (debugMode) {
                                    console.log("Số phân đoạn:", totalPrizes);
                                    console.log("Góc mỗi phân đoạn:", anglePerSegment);
                                    console.log("Tổng góc quay:", totalRotation);
                                }

                                // Bắt đầu animation quay
                                spinToPosition(wheel, tempRotation, totalRotation, duration, function() {
                                    // Kiểm tra xem vị trí hiện tại có chính xác không
                                    const isPositionCorrect = verifySpinResult(prize);

                                    if (isPositionCorrect) {
                                        // Hiển thị kết quả khi quay xong và vị trí chính xác
                                        showPrizeResult(prize, true);
                                        isSpinning = false;

                                        // Disable nút quay vĩnh viễn (mỗi người chỉ được quay 1 lần)
                                        $('#spin-button').prop('disabled', true).text('Đã sử dụng lượt quay');
                                    } else {
                                        // Nếu vị trí không chính xác, điều chỉnh
                                        adjustToCorrectPosition(prize, function() {
                                            showPrizeResult(prize, true);
                                            isSpinning = false;
                                            $('#spin-button').prop('disabled', true).text('Đã sử dụng lượt quay');
                                        });
                                    }
                                });
                            } else {
                                console.error("Không tìm thấy phân đoạn cho giải thưởng:", prize.id);
                                isSpinning = false;
                                $('#spin-button').prop('disabled', false).text('QUAY NGAY!');
                            }
                        } else {
                            // Nếu không trúng thưởng, quay đến một vị trí ngẫu nhiên (không phải vị trí của giải thưởng)
                            const totalPrizes = prizes.length;
                            const anglePerSegment = 360 / totalPrizes;

                            // Tạo một góc ngẫu nhiên
                            const randomAngle = Math.floor(Math.random() * 360);

                            // Số vòng quay cố định + góc ngẫu nhiên
                            const rotations = wheelConfig.spinEffect?.rotations || 5;
                            const totalRotation = (rotations * 360) + randomAngle;
                            const duration = wheelConfig.spinEffect?.duration || 5000;

                            // Bắt đầu animation quay
                            spinToPosition(wheel, tempRotation, totalRotation, duration, function() {
                                // Hiển thị kết quả không trúng thưởng
                                showPrizeResult(null, false);
                                isSpinning = false;

                                // Disable nút quay vĩnh viễn (mỗi người chỉ được quay 1 lần)
                                $('#spin-button').prop('disabled', true).text('Đã sử dụng lượt quay');
                            });
                        }
                    } else {
                        // Nếu có lỗi từ server
                        Swal.fire({
                            icon: 'error',
                            title: 'Có lỗi xảy ra',
                            text: response.message || 'Không thể quay thưởng. Vui lòng thử lại sau.',
                            confirmButtonColor: '#dc3545'
                        });

                        isSpinning = false;
                        $('#spin-button').prop('disabled', false).text('QUAY NGAY!');
                    }
                },
                error: function(xhr) {
                    // Dừng hiệu ứng quay tạm thời
                    clearInterval(tempRotationInterval);

                    let errorMessage = 'Đã xảy ra lỗi khi quay thưởng.';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Quay thưởng thất bại!',
                        text: errorMessage,
                        confirmButtonColor: '#dc3545'
                    });

                    isSpinning = false;
                    $('#spin-button').prop('disabled', false).text('QUAY NGAY!');
                }
            });
        }
    });

    // Hàm quay vòng quay đến vị trí xác định
    function spinToPosition(wheel, startRotation, targetRotation, duration, callback) {
        let startTime = null;
        // Sử dụng hàm easing mượt mà hơn để có hiệu ứng tự nhiên
        const easeOut = t => (--t) * t * t + 1;

        function animate(currentTime) {
            if (!startTime) startTime = currentTime;
            const elapsed = currentTime - startTime;

            if (elapsed <= duration) {
                // Tính toán góc quay hiện tại dựa trên thời gian đã trôi qua
                const progress = elapsed / duration;
                const easedProgress = easeOut(progress);
                const currentRotation = startRotation + (targetRotation * easedProgress);

                // Áp dụng góc quay
                wheel.style.transform = `rotate(${currentRotation}deg)`;

                // Tiếp tục animation
                requestAnimationFrame(animate);
            } else {
                // Đảm bảo vòng quay dừng ở đúng vị trí mục tiêu
                const finalRotation = startRotation + targetRotation;
                wheel.style.transform = `rotate(${finalRotation}deg)`;

                // Lưu lại góc quay cuối cùng để tham chiếu sau này
                wheel.dataset.currentRotation = finalRotation;

                // Thêm class để đánh dấu vòng quay đã dừng
                wheel.classList.add('wheel-stopped');

                // Gọi callback khi hoàn tất
                if (typeof callback === 'function') {
                    // Thêm timeout nhỏ để đảm bảo animation đã hoàn tất
                    setTimeout(() => {
                        callback();
                    }, 100);
                }
            }
        }

        requestAnimationFrame(animate);
    }

    // Load danh sách tỉnh/thành phố từ API
    $.getJSON(provincesUrl, function(response) {
        if (response.success) {
            $.each(response.data, function(index, province) {
                $('#province').append($('<option>', {
                    value: province.name,
                    text: province.name,
                    'data-id': province.id
                }));
            });
        }
    });

    // Load danh sách quận/huyện khi chọn tỉnh/thành phố
    $('#province').change(function() {
        const provinceId = $(this).find(':selected').data('id');
        $('#district').prop('disabled', true).empty().append($('<option>', {
            value: '',
            text: 'Chọn Quận/Huyện'
        }));

        $('#ward').prop('disabled', true).empty().append($('<option>', {
            value: '',
            text: 'Chọn Phường/Xã'
        }));

        if (provinceId) {
            $.getJSON(`${apiBaseUrl}/provinces/${provinceId}/districts`, function(response) {
                if (response.success) {
                    $.each(response.data, function(index, district) {
                        $('#district').append($('<option>', {
                            value: district.name,
                            text: district.name,
                            'data-id': district.id
                        }));
                    });
                    $('#district').prop('disabled', false);
                }
            });
        }
    });

    // Load danh sách phường/xã khi chọn quận/huyện
    $('#district').change(function() {
        const districtId = $(this).find(':selected').data('id');
        $('#ward').prop('disabled', true).empty().append($('<option>', {
            value: '',
            text: 'Chọn Phường/Xã'
        }));

        if (districtId) {
            $.getJSON(`${apiBaseUrl}/districts/${districtId}/wards`, function(response) {
                if (response.success) {
                    $.each(response.data, function(index, ward) {
                        $('#ward').append($('<option>', {
                            value: ward.name,
                            text: ward.name
                        }));
                    });
                    $('#ward').prop('disabled', false);
                }
            });
        }
    });

    // Kiểm tra và chuẩn bị vòng quay
    function checkWheelSetup() {
        // Hiển thị viền phân đoạn nếu debug mode bật
        if (debugMode) {
            // Hiển thị chỉ dẫn phân đoạn bằng chữ số
            const segments = document.querySelectorAll('.wheel-segment');
            segments.forEach((segment, index) => {
                const path = segment.querySelector('path');
                if (path) {
                    path.setAttribute('stroke-width', '1');
                    path.setAttribute('stroke', '#056839');
                }

                // Thêm số thứ tự vào phân đoạn để dễ theo dõi
                const prizeId = segment.dataset.prizeId;
                const prizeName = segment.dataset.prizeName;
                console.log(`Phân đoạn ${index}: ID=${prizeId}, Tên=${prizeName}`);
            });

            // Làm nổi bật mũi tên
            const pointer = document.querySelector('.wheel-pointer');
            if (pointer) {
                pointer.style.zIndex = '100';
                pointer.style.transition = 'all 0.3s';

                // Thêm hiệu ứng hover để kiểm tra vị trí mũi tên
                pointer.addEventListener('mouseover', function() {
                    this.style.filter = 'drop-shadow(0 0 8px red)';
                });

                pointer.addEventListener('mouseout', function() {
                    this.style.filter = 'drop-shadow(0 0 8px rgba(0, 0, 0, 0.5))';
                });
            }

            console.log("DEBUG MODE: Vòng quay đã được chuẩn bị với chế độ debug");
            console.log(`Offset hiện tại: ${wheelConfig.angleOffset} phân đoạn`);
            console.log(`Mỗi phân đoạn: ${360/prizes.length}°`);
        }
    }

    // Khởi tạo ứng dụng
    function init() {
        // Khởi tạo CSRF token cho AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Khởi tạo vòng quay
        initializeWheel();

        // Khởi tạo modal
        initializeModal();

        $('.wheel-center').hover(function() {
            $(this).find('span').css('transform', 'scale(1.1)');
        }, function() {
            $(this).find('span').css('transform', 'scale(1)');
        });

        // Kiểm tra thiết lập vòng quay
        if (debugMode) {
            checkWheelSetup();
        }

        // Tải danh sách tỉnh/thành phố
        loadProvinces();

        // Nếu đã debug, thiết lập ngẫu nhiên
        if (debugMode) {
            setupDebugEnvironment();
        }
    }

    // Chạy hàm khởi tạo
    init();

    // Hàm kiểm tra vị trí mũi tên và phân đoạn
    function checkPointerPosition() {
        const wheel = document.getElementById('wheel');
        if (!wheel) return null;

        // Lấy góc quay hiện tại
        const currentRotation = parseFloat(wheel.dataset.currentRotation || 0);

        // Tính toán phân đoạn hiện tại dựa trên góc quay
        const totalPrizes = prizes.length;
        const anglePerSegment = 360 / totalPrizes;

        // Tính toán góc tương đối (0-360)
        const relativeAngle = currentRotation % 360;
        // Chuyển đổi góc tương đối thành góc dương (0-360)
        const positiveAngle = relativeAngle < 0 ? relativeAngle + 360 : relativeAngle;

        // Tính toán phân đoạn hiện tại
        // Công thức: Math.floor((360 - positiveAngle) / anglePerSegment) % totalPrizes
        const currentSegment = Math.floor((360 - positiveAngle) / anglePerSegment) % totalPrizes;

        // Tìm giải thưởng tương ứng với phân đoạn hiện tại
        const currentPrize = prizes[currentSegment];

        if (debugMode) {
            console.log("Kiểm tra vị trí mũi tên:");
            console.log("Góc quay hiện tại:", currentRotation);
            console.log("Góc tương đối (0-360):", positiveAngle);
            console.log("Phân đoạn hiện tại:", currentSegment);
            console.log("Giải thưởng hiện tại:", currentPrize);
        }

        return {
            segment: currentSegment,
            prize: currentPrize,
            angle: positiveAngle
        };
    }

    // Hàm xác nhận kết quả quay
    function verifySpinResult(expectedPrize) {
        const pointerPosition = checkPointerPosition();
        if (!pointerPosition || !pointerPosition.prize) return false;

        // Nếu không có giải thưởng kỳ vọng, luôn trả về true
        if (!expectedPrize) return true;

        // So sánh ID giải thưởng
        const isCorrect = parseInt(pointerPosition.prize.id) === parseInt(expectedPrize.id);

        if (debugMode) {
            console.log("Xác nhận kết quả quay:");
            console.log("Giải thưởng kỳ vọng:", expectedPrize);
            console.log("Giải thưởng thực tế:", pointerPosition.prize);
            console.log("Kết quả chính xác:", isCorrect);
        }
        // Nếu vị trí không chính xác, điều chỉnh vòng quay
        if (!isCorrect && wheelConfig.ensureExactPointer) {
            // Thêm callback để hiển thị kết quả sau khi điều chỉnh xong
            adjustToCorrectPosition(expectedPrize, function() {
                showPrizeResult(expectedPrize, true);
            });
            return false;
        }
        return isCorrect;
    }

    // Hàm điều chỉnh vòng quay đến vị trí chính xác của giải thưởng
    function adjustToCorrectPosition(expectedPrize, callback) {
        // Tìm phân đoạn tương ứng với giải thưởng mong đợi
        const prizeSegmentIndex = findSegmentByPrizeId(expectedPrize.id);
        if (prizeSegmentIndex === -1) {
            console.error("Không tìm thấy phân đoạn cho giải thưởng:", expectedPrize);
            if (typeof callback === 'function') callback();
            return;
        }

        const wheel = document.getElementById('wheel');
        if (!wheel) return;

        // Lấy góc quay hiện tại
        const currentRotation = parseFloat(wheel.dataset.currentRotation || 0);

        // Tính toán góc cần quay đến
        const totalPrizes = prizes.length;
        const anglePerSegment = 360 / totalPrizes;

        // Góc cơ bản để mũi tên trỏ vào giữa phân đoạn
        let targetAngle = 360 - (prizeSegmentIndex * anglePerSegment) - (anglePerSegment / 2);

        // Đảm bảo targetAngle nằm trong khoảng [0, 360)
        targetAngle = targetAngle % 360;
        if (targetAngle < 0) targetAngle += 360;

        // Bù trừ góc cho mũi tên
        const offsetMultiplier = wheelConfig.angleOffset || 0;
        const offsetAngle = anglePerSegment * offsetMultiplier;
        const adjustedAngle = targetAngle + offsetAngle;

        // Đảm bảo adjustedAngle nằm trong khoảng [0, 360)
        const finalTargetAngle = adjustedAngle % 360;

        // Tính toán góc quay cần điều chỉnh (góc tương đối)
        // Cần tính toán sao cho vòng quay quay theo đường ngắn nhất đến vị trí mới
        const currentAngleNormalized = currentRotation % 360;
        const currentPositiveAngle = currentAngleNormalized < 0 ? currentAngleNormalized + 360 : currentAngleNormalized;

        // Tìm đường đi ngắn nhất (theo chiều kim đồng hồ hoặc ngược chiều)
        let adjustmentAngle = finalTargetAngle - currentPositiveAngle;

        // Đảm bảo góc điều chỉnh theo đường ngắn nhất
        if (Math.abs(adjustmentAngle) > 180) {
            adjustmentAngle = adjustmentAngle > 0 ? adjustmentAngle - 360 : adjustmentAngle + 360;
        }

        // Thêm một vòng quay đầy đủ (360 độ) trước khi đến vị trí giải thưởng
        adjustmentAngle += 360;

        if (debugMode) {
            console.log("Điều chỉnh vị trí vòng quay:");
            console.log("Góc hiện tại:", currentPositiveAngle);
            console.log("Góc mục tiêu:", finalTargetAngle);
            console.log("Góc điều chỉnh (với 1 vòng bổ sung):", adjustmentAngle);
        }

        // Quay chậm đến vị trí chính xác (thời gian quay tỷ lệ với góc điều chỉnh)
        // Điều chỉnh tốc độ quay cho phù hợp - đảm bảo tổng thời gian quay khoảng 3.6 giây
        const totalDegrees = Math.abs(adjustmentAngle);
        const adjustmentDuration = 3600; // 3.6 giây - thời gian cố định cho animation

        if (debugMode) {
            console.log("Tổng độ cần quay:", totalDegrees);
            console.log("Thời gian điều chỉnh:", adjustmentDuration + "ms");
        }

        // Thêm class để có hiệu ứng chuyển động mượt hơn
        wheel.classList.add('wheel-adjusting');

        // Sử dụng hàm spinToPosition với góc điều chỉnh
        spinToPosition(wheel, currentRotation, adjustmentAngle, adjustmentDuration, function() {
            if (debugMode) {
                console.log("Đã điều chỉnh vòng quay đến vị trí chính xác");
            }

            // Xóa class điều chỉnh
            wheel.classList.remove('wheel-adjusting');

            // Kiểm tra lại vị trí sau khi điều chỉnh
            const newPosition = checkPointerPosition();
            if (newPosition && newPosition.prize) {
                if (parseInt(newPosition.prize.id) === parseInt(expectedPrize.id)) {
                    if (debugMode) {
                        console.log("Vị trí sau điều chỉnh chính xác");
                    }
                } else {
                    console.error("Vị trí sau điều chỉnh vẫn không chính xác:", newPosition.prize);
                }
            }

            // Gọi callback sau khi điều chỉnh xong
            if (typeof callback === 'function') callback();
        });
    }
});



