@extends('admin.layout.app')

@section('title', 'Thêm giải thưởng mới')

@section('header', 'Thêm giải thưởng mới - ' . $luckyWheel->name)

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Thêm giải thưởng mới</h5>
            <a href="{{ route('admin.lucky-wheels.prizes.index', $luckyWheel) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.lucky-wheels.prizes.store', $luckyWheel) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Tên giải thưởng <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Hình ảnh</label>
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Hình ảnh nên có kích thước vuông và không quá 2MB.</div>
            </div>

            <div class="mb-3">
                <label for="background_color" class="form-label">Màu nền phân đoạn</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-palette"></i></span>
                    <input type="color" class="form-control form-control-color @error('background_color') is-invalid @enderror" id="background_color" name="background_color" value="{{ old('background_color', '#e74c3c') }}">
                </div>
                @error('background_color')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Chọn màu nền cho phân đoạn trên vòng quay.</div>
            </div>

            <div class="mb-3">
                <label for="icon" class="form-label">Biểu tượng</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-icons"></i></span>
                    <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon', 'fa-gift') }}" placeholder="fa-gift">
                </div>
                @error('icon')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Nhập tên biểu tượng Font Awesome (ví dụ: fa-gift, fa-money-bill, fa-trophy). <a href="https://fontawesome.com/icons" target="_blank">Xem danh sách biểu tượng</a></div>
            </div>

            <div class="mb-3">
                <label for="win_rate" class="form-label">Tỷ lệ trúng thưởng (%) <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('win_rate') is-invalid @enderror" id="win_rate" name="win_rate" value="{{ old('win_rate', 5) }}" min="0" max="100" step="0.01" required>
                @error('win_rate')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Tỷ lệ trúng thưởng từ 0% đến 100%.</div>
            </div>

            <div class="mb-3">
                <label for="quantity" class="form-label">Số lượng giải thưởng <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', 10) }}" min="0" required>
                @error('quantity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="remaining" class="form-label">Số lượng còn lại <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('remaining') is-invalid @enderror" id="remaining" name="remaining" value="{{ old('remaining', 10) }}" min="0" required>
                @error('remaining')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Số lượng giải thưởng còn lại ban đầu.</div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Xem trước</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="preview-segment" id="preview-segment">
                    <div class="preview-content" id="preview-content">
                        <i class="fas fa-gift" id="preview-icon"></i>
                        <div class="preview-name" id="preview-name">Tên giải thưởng</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <p>Xem trước phân đoạn trên vòng quay.</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .preview-segment {
        width: 200px;
        height: 100px;
        overflow: hidden;
        position: relative;
        margin: 0 auto;
        border-radius: 10px;
    }

    .preview-content {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background-color: #e74c3c;
        color: white;
        text-align: center;
        padding: 10px;
    }

    .preview-content i {
        font-size: 24px;
        margin-bottom: 8px;
    }

    .preview-name {
        font-weight: bold;
        font-size: 14px;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Cập nhật xem trước khi thay đổi
        function updatePreview() {
            const backgroundColor = $('#background_color').val();
            const icon = $('#icon').val();
            const name = $('#name').val() || 'Tên giải thưởng';

            $('#preview-content').css('background-color', backgroundColor);
            $('#preview-icon').attr('class', 'fas ' + icon);
            $('#preview-name').text(name);
        }

        // Gắn sự kiện cho các trường
        $('#background_color, #icon, #name').on('input', updatePreview);

        // Cập nhật xem trước ban đầu
        updatePreview();
    });
</script>
@endpush
