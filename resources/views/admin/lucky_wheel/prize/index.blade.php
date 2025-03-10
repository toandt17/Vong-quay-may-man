@extends('admin.layout.app')

@section('title', 'Danh sách giải thưởng')

@section('header', 'Danh sách giải thưởng - ' . $luckyWheel->name)

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Danh sách giải thưởng</h5>
            <div>
                <a href="{{ route('admin.lucky-wheels.prizes.create', $luckyWheel) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm giải thưởng
                </a>
                <a href="{{ route('admin.lucky-wheels.show', $luckyWheel) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Màu sắc</th>
                        <th>Biểu tượng</th>
                        <th>Tên giải thưởng</th>
                        <th>Hình ảnh</th>
                        <th>Tỷ lệ (%)</th>
                        <th>Số lượng</th>
                        <th>Còn lại</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prizes as $prize)
                    <tr>
                        <td>{{ $prize->id }}</td>
                        <td>
                            <div class="color-preview" style="background-color: {{ $prize->background_color ?: '#e74c3c' }}"></div>
                        </td>
                        <td>
                            @if($prize->icon)
                                <i class="fas {{ $prize->icon }}"></i>
                            @else
                                <i class="fas fa-gift"></i>
                            @endif
                        </td>
                        <td>{{ $prize->name }}</td>
                        <td>
                            @if($prize->image)
                                <img src="{{ asset('storage/' . $prize->image) }}" alt="{{ $prize->name }}" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">
                            @else
                                <span class="text-muted">Không có</span>
                            @endif
                        </td>
                        <td>{{ number_format($prize->win_rate, 2) }}%</td>
                        <td>{{ $prize->quantity }}</td>
                        <td>{{ $prize->remaining }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.lucky-wheels.prizes.edit', ['lucky_wheel' => $luckyWheel, 'prize' => $prize]) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $prize->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <!-- Modal xác nhận xóa -->
                            <div class="modal fade" id="deleteModal{{ $prize->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $prize->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $prize->id }}">Xác nhận xóa</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Bạn có chắc chắn muốn xóa giải thưởng <strong>{{ $prize->name }}</strong> không?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                            <form action="{{ route('admin.lucky-wheels.prizes.destroy', ['lucky_wheel' => $luckyWheel, 'prize' => $prize]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Xóa</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">Không có giải thưởng nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Xem trước vòng quay</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="wheel-preview-container">
                    <div class="wheel-preview" id="wheel-preview">
                        <!-- Các phân đoạn sẽ được tạo bằng JavaScript -->
                    </div>
                    <div class="wheel-center-preview">
                        <span>QUAY<br>NGAY</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-info">
                    <h5>Thông tin vòng quay</h5>
                    <p><strong>Tên:</strong> {{ $luckyWheel->name }}</p>
                    <p><strong>Mô tả:</strong> {{ $luckyWheel->description ?: 'Không có mô tả' }}</p>
                    <p><strong>Trạng thái:</strong> {!! $luckyWheel->is_active ? '<span class="badge bg-success">Đang hoạt động</span>' : '<span class="badge bg-danger">Không hoạt động</span>' !!}</p>
                    <p><strong>Tổng số giải thưởng:</strong> {{ $prizes->count() }}</p>
                    <p><strong>Tổng tỷ lệ trúng thưởng:</strong> {{ number_format($prizes->sum('win_rate'), 2) }}%</p>
                </div>
                <div class="alert alert-warning">
                    <p><i class="fas fa-info-circle"></i> Lưu ý: Tổng tỷ lệ trúng thưởng nên nhỏ hơn hoặc bằng 100%.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .color-preview {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: inline-block;
        border: 1px solid #ddd;
    }

    .wheel-preview-container {
        position: relative;
        width: 300px;
        height: 300px;
        margin: 0 auto;
    }

    .wheel-preview {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        overflow: hidden;
        position: relative;
        border: 5px solid white;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }

    .wheel-segment-preview {
        position: absolute;
        width: 50%;
        height: 50%;
        transform-origin: bottom right;
        left: 0;
        top: 0;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    .segment-content-preview {
        position: absolute;
        left: -100%;
        width: 200%;
        height: 200%;
        transform-origin: 100% 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
        font-size: 0.7rem;
        padding-bottom: 45%;
    }

    .segment-content-preview i {
        font-size: 1rem;
        margin-bottom: 5px;
    }

    .wheel-center-preview {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 25%;
        height: 25%;
        background-color: #4169e1;
        background-image: linear-gradient(135deg, #4169e1, #1e3a8a);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffeb3b;
        font-weight: bold;
        text-align: center;
        z-index: 10;
        border: 3px solid white;
        font-size: 0.7rem;
        text-transform: uppercase;
        line-height: 1.1;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Tạo xem trước vòng quay
        function createWheelPreview() {
            const wheel = document.getElementById('wheel-preview');
            const prizes = @json($prizes);
            const totalPrizes = prizes.length;
            const anglePerSegment = 360 / totalPrizes;

            // Xóa tất cả các phân đoạn hiện có (nếu có)
            wheel.innerHTML = '';

            // Tạo các phân đoạn với kích thước bằng nhau
            for (let i = 0; i < totalPrizes; i++) {
                const prize = prizes[i];
                const segment = document.createElement('div');
                segment.className = 'wheel-segment-preview';
                segment.style.transform = `rotate(${i * anglePerSegment}deg)`;

                const content = document.createElement('div');
                content.className = 'segment-content-preview';
                content.style.transform = `rotate(${anglePerSegment / 2}deg)`;
                content.style.backgroundColor = prize.background_color || '#e74c3c';

                // Thêm biểu tượng
                const icon = document.createElement('i');
                icon.className = `fas ${prize.icon || 'fa-gift'}`;
                content.appendChild(icon);

                // Thêm tên giải thưởng
                const nameElement = document.createElement('div');
                nameElement.textContent = prize.name;
                nameElement.style.fontWeight = 'bold';
                content.appendChild(nameElement);

                segment.appendChild(content);
                wheel.appendChild(segment);
            }
        }

        // Gọi hàm tạo xem trước vòng quay
        createWheelPreview();
    });
</script>
@endpush
