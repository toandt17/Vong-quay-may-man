@extends('admin.layout.app')

@section('title', 'Chỉnh sửa vòng quay may mắn')

@section('header', 'Chỉnh sửa vòng quay may mắn')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Chỉnh sửa vòng quay may mắn</h5>
            <a href="{{ route('admin.lucky-wheels.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.lucky-wheels.update', $luckyWheel) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Tên vòng quay <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $luckyWheel->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $luckyWheel->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input @error('is_active') is-invalid @enderror" id="is_active" name="is_active" value="1" {{ old('is_active', $luckyWheel->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Kích hoạt vòng quay</label>
                @error('is_active')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Nếu được chọn, vòng quay này sẽ được hiển thị cho người dùng.</div>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Bạn có thể quản lý các giải thưởng của vòng quay này bằng cách nhấp vào nút "Quản lý giải thưởng" sau khi lưu thay đổi.
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Quản lý giải thưởng</h5>
            <a href="{{ route('admin.lucky-wheels.prizes.index', $luckyWheel) }}" class="btn btn-primary">
                <i class="fas fa-gift"></i> Quản lý giải thưởng
            </a>
        </div>
    </div>
    <div class="card-body">
        <p>Vòng quay này hiện có <strong>{{ $luckyWheel->prizes->count() }}</strong> giải thưởng.</p>
        <p>Nhấp vào nút "Quản lý giải thưởng" để xem, thêm, sửa hoặc xóa các giải thưởng của vòng quay này.</p>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Thống kê</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title">Thông tin giải thưởng</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Tổng số giải thưởng
                                <span class="badge bg-primary rounded-pill">{{ $luckyWheel->prizes->count() }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Tổng số lượng giải thưởng
                                <span class="badge bg-primary rounded-pill">{{ $luckyWheel->prizes->sum('quantity') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Số lượng còn lại
                                <span class="badge bg-success rounded-pill">{{ $luckyWheel->prizes->sum('remaining') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Tổng tỷ lệ trúng thưởng
                                <span class="badge bg-info rounded-pill">{{ number_format($luckyWheel->prizes->sum('win_rate'), 2) }}%</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title">Thông tin người tham gia</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Tổng số người tham gia
                                <span class="badge bg-primary rounded-pill">{{ $luckyWheel->awardHistories->count() }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Số người trúng thưởng
                                <span class="badge bg-success rounded-pill">{{ $luckyWheel->awardHistories->where('is_win', true)->count() }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Số người không trúng thưởng
                                <span class="badge bg-danger rounded-pill">{{ $luckyWheel->awardHistories->where('is_win', false)->count() }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
