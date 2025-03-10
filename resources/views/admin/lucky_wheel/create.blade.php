@extends('admin.layout.app')

@section('title', 'Thêm vòng quay may mắn mới')

@section('header', 'Thêm vòng quay may mắn mới')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Thêm vòng quay may mắn mới</h5>
            <a href="{{ route('admin.lucky-wheels.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.lucky-wheels.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Tên vòng quay <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input @error('is_active') is-invalid @enderror" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Kích hoạt vòng quay</label>
                @error('is_active')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Nếu được chọn, vòng quay này sẽ được hiển thị cho người dùng.</div>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Sau khi tạo vòng quay, bạn có thể thêm các giải thưởng vào vòng quay.
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
