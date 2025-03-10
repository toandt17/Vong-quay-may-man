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
        const textDistance = 35; // Cách tâm 35% bán kính cho rõ hơn
        const textX = 50 + textDistance * Math.cos(midRad);
        const textY = 50 + textDistance * Math.sin(midRad);

        const text = document.createElement('div');
        text.className = 'wheel-segment-text';
        text.textContent = prize.name;
        text.style.left = textX + '%';
        text.style.top = textY + '%';

        // Cố định hướng của chữ theo chiều đọc từ ngoài vào trong (hướng về tâm)
        const textRotation = midAngle + 90; // Cố định hướng text

        text.style.transform = `translate(-50%, -50%) rotate(${textRotation}deg)`;
        text.style.color = colors.text;
        text.style.fontWeight = 'bold';
        text.style.fontSize = '14px';
        text.style.textShadow = '1px 1px 2px rgba(0,0,0,0.5)';

        wheel.appendChild(text);

        // Thêm hình ảnh nếu có
        if (prize.image) {
            const imgDistance = 70; // Cách tâm 70% bán kính, xa hơn một chút
            const imgX = 50 + imgDistance * Math.cos(midRad);
            const imgY = 50 + imgDistance * Math.sin(midRad);

            const imgContainer = document.createElement('div');
            imgContainer.className = 'wheel-image';
            imgContainer.style.left = imgX + '%';
            imgContainer.style.top = imgY + '%';
            imgContainer.style.transform = `translate(-50%, -50%) rotate(${textRotation}deg)`;
            imgContainer.style.width = '45px';
            imgContainer.style.height = '45px';

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


// ...existing code...

    // Hàm tìm vị trí phân đoạn dựa vào ID giải thưởng
    function findSegmentByPrizeId(prizeId) {
        // Kiểm tra trong map trước
        if (prizeIdToIndexMap.hasOwnProperty(prizeId)) {
            return prizeIdToIndexMap[prizeId];
        }

        // Tìm trong mảng prizes
        const index = prizes.findIndex(p => parseInt(p.id) === parseInt(prizeId));
        if (index !== -1) {
            return index;
        }

        // Không tìm thấy, trả về -1
        console.error("Không tìm thấy giải thưởng ID:", prizeId);
        return -1;
    }

    // Hiển thị kết quả trúng thưởng
    function showPrizeResult(prize, isWin) {
        if (isWin && prize) {
            // Trúng thưởng
            $('#result-container').removeClass('lose-result').addClass('win-result');
            $('#result-title').text('Chúc mừng!');
            $('#result-message').text(`Bạn đã trúng ${prize.name}!`);

            // Hiển thị thông tin chi tiết giải thưởng
            $('#prize-name').text(prize.name);
            $('#prize-description').text(prize.description || '');

            // Hiển thị hình ảnh nếu có
            if (prize.image) {
                $('#prize-image').attr('src', prize.image);
                $('#prize-image').show();
            } else {
                $('#prize-image').hide();
            }

            // Hiển thị số lượng nếu có
            if (prize.quantity) {
                $('#prize-quantity').text(`Còn lại: ${prize.remaining}/${prize.quantity}`);
            } else {
                $('#prize-quantity').text('');
            }

            $('#prize-details').show();
        } else {
            // Không trúng thưởng
            $('#result-container').removeClass('win-result').addClass('lose-result');
            $('#result-title').text('Rất tiếc!');
            $('#result-message').text('Bạn không trúng giải lần này. Chúc may mắn lần sau!');
            $('#prize-details').hide();
        }

        // Hiển thị kết quả với hiệu ứng
        $('#result-container').fadeIn().addClass('show');
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

        // Hiển thị/ẩn trường giống lúa khi chọn là nông dân (đã chuyển sang modal)
        $('#is_farmer').change(function() {
            if($(this).is(':checked')) {
                $('.farmer-field').show();
            } else {
                $('.farmer-field').hide();
            }
        });
    }

    // Hàm submit form đăng ký từ modal
    function submitRegistrationForm() {
        // Kiểm tra validate form
        const form = document.getElementById('registration-form');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Disable nút submit
        $('#submit-registration').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...');

        // Lấy dữ liệu form
        const formData = {
            name: $('#name').val(),
            phone: $('#phone').val(),
            province: $('#province').val(),
            district: $('#district').val(),
            ward: $('#ward').val(),
            address: $('#address').val(),
            is_farmer: $('#is_farmer').is(':checked') ? 1 : 0,
            rice_variety: $('#rice_variety').val(),
            used_products: $('#used_products').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        // Gửi request đăng ký
        $.ajax({
            url: registerUrl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Đóng modal đăng ký
                    registerModal.hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Đăng ký thành công!',
                        text: response.message,
                        confirmButtonColor: '#28a745'
                    });

                    // Lưu ID người tham gia
                    participantId = response.participant_id;
                    isRegistered = true;

                    // Enable nút quay
                    $('#spin-button').prop('disabled', false);

                    // Hiển thị thông báo hướng dẫn
                    showWheelInstructions();

                    // Animate wheel section
                    if (typeof window.animateWheelAfterRegistration === 'function') {
                        window.animateWheelAfterRegistration();
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Đăng ký thất bại!',
                        text: response.message,
                        confirmButtonColor: '#dc3545'
                    });

                    // Enable lại nút submit
                    $('#submit-registration').prop('disabled', false).html('ĐĂNG KÝ');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Đã xảy ra lỗi khi đăng ký.';

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors)[0][0];
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Đăng ký thất bại!',
                    text: errorMessage,
                    confirmButtonColor: '#dc3545'
                });

                // Enable lại nút submit
                $('#submit-registration').prop('disabled', false).html('ĐĂNG KÝ');
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

    // Tương tác cho nút ở giữa vòng quay
    $(document).on('click', '.wheel-center', function() {
        if (!$('#spin-button').prop('disabled') && !isSpinning) {
            $('#spin-button').trigger('click');
        }
    });

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
                            const targetAngle = 360 - (prizeSegmentIndex * anglePerSegment) - (anglePerSegment / 2);

                            // Bù trừ góc cho mũi tên
                            // Nếu góc offset âm, tức là cần dịch chuyển ngược lại
                            const offsetMultiplier = wheelConfig.angleOffset || 0;
                            const offsetAngle = anglePerSegment * offsetMultiplier;
                            const adjustedAngle = targetAngle + offsetAngle;

                            if (debugMode) {
                                console.log(`Góc cơ bản: ${targetAngle}°`);
                                console.log(`Điều chỉnh góc: ${offsetAngle}° (${offsetMultiplier} phân đoạn)`);
                                console.log(`Góc cuối cùng: ${adjustedAngle}°`);
                            }

                            const rotations = wheelConfig.spinEffect?.rotations || 5; // Số vòng quay cố định
                            const totalRotation = (rotations * 360) + adjustedAngle;
                            const duration = wheelConfig.spinEffect?.duration || 5000; // Thời gian quay

                            if (debugMode) {
                                console.log("Số phân đoạn:", totalPrizes);
                                console.log("Góc mỗi phân đoạn:", anglePerSegment);
                                console.log("Phân đoạn mục tiêu:", prizeSegmentIndex);
                                console.log("Tổng góc quay:", totalRotation);
                            }

                            // Bắt đầu animation quay
                            spinToPosition(wheel, tempRotation, totalRotation, duration, function() {
                                // Hiển thị kết quả khi quay xong
                                showPrizeResult(prize, true);
                                isSpinning = false;

                                // Disable nút quay vĩnh viễn (mỗi người chỉ được quay 1 lần)
                                $('#spin-button').prop('disabled', true).text('Đã sử dụng lượt quay');
                            });
                        } else {
                            // Không tìm thấy phân đoạn - hiển thị lỗi
                            console.error("Không tìm thấy phân đoạn cho giải thưởng:", prize);
                            isSpinning = false;
                            $('#spin-button').prop('disabled', false).text('QUAY NGAY!');

                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi vòng quay!',
                                text: 'Không thể xác định vị trí giải thưởng trên vòng quay.',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    } else {
                        // Không trúng thưởng - quay đến một vị trí ngẫu nhiên
                        const randomSegment = Math.floor(Math.random() * prizes.length);
                        const anglePerSegment = 360 / prizes.length;

                        // Tính toán góc cơ bản
                        const targetAngle = 360 - (randomSegment * anglePerSegment) - (anglePerSegment / 2);

                        // Áp dụng offset tương tự
                        const offsetMultiplier = wheelConfig.angleOffset || 0;
                        const offsetAngle = anglePerSegment * offsetMultiplier;
                        const adjustedAngle = targetAngle + offsetAngle;

                        const rotations = wheelConfig.spinEffect?.rotations || 5; // Số vòng quay cố định
                        const totalRotation = (rotations * 360) + adjustedAngle;
                        const duration = wheelConfig.spinEffect?.duration || 5000; // Thời gian quay

                        // Bắt đầu animation quay
                        spinToPosition(wheel, tempRotation, totalRotation, duration, function() {
                            // Hiển thị kết quả khi quay xong
                            showPrizeResult(null, false);
                            isSpinning = false;

                            // Disable nút quay vĩnh viễn (mỗi người chỉ được quay 1 lần)
                            $('#spin-button').prop('disabled', true).text('Đã sử dụng lượt quay');
                        });
                    }
                } else {
                    // Server trả về lỗi
                    isSpinning = false;
                    $('#spin-button').prop('disabled', false).text('QUAY NGAY!');

                    Swal.fire({
                        icon: 'error',
                        title: 'Quay thưởng thất bại!',
                        text: response.message,
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            error: function() {
                // Dừng hiệu ứng quay tạm thời
                clearInterval(tempRotationInterval);
                isSpinning = false;

                Swal.fire({
                    icon: 'error',
                    title: 'Quay thưởng thất bại!',
                    text: 'Đã xảy ra lỗi khi quay thưởng. Vui lòng thử lại sau.',
                    confirmButtonColor: '#dc3545'
                });

                // Enable lại nút quay
                $('#spin-button').prop('disabled', false).text('QUAY NGAY!');
            }
        });
    });

    // Hàm quay vòng quay đến vị trí xác định
    function spinToPosition(wheel, startRotation, targetRotation, duration, callback) {
        let startTime = null;
        const easeOut = t => 1 - Math.pow(1 - t, 3); // Hàm easing mượt mà

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
                // Đảm bảo vòng quay dừng ở đúng vị trí
                wheel.style.transform = `rotate(${startRotation + targetRotation}deg)`;

                // Gọi callback khi hoàn tất
                if (typeof callback === 'function') {
                    callback();
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
});

