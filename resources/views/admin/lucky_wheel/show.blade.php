@extends('admin.layout.app')

@section('title', 'Chi tiết vòng quay may mắn')

@section('header', 'Chi tiết vòng quay may mắn')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Chi tiết vòng quay may mắn</h5>
            <div>
                <a href="{{ route('admin.lucky-wheels.prizes.index', $luckyWheel) }}" class="btn btn-primary">
                    <i class="fas fa-gift"></i> Quản lý giải thưởng
                </a>
                <a href="{{ route('admin.lucky-wheels.edit', $luckyWheel) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Chỉnh sửa
                </a>
                <a href="{{ route('admin.lucky-wheels.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">ID</th>
                        <td>{{ $luckyWheel->id }}</td>
                    </tr>
                    <tr>
                        <th>Tên</th>
                        <td>{{ $luckyWheel->name }}</td>
                    </tr>
                    <tr>
                        <th>Mô tả</th>
                        <td>{{ $luckyWheel->description ?: 'Không có mô tả' }}</td>
                    </tr>
                    <tr>
                        <th>Trạng thái</th>
                        <td>
                            @if($luckyWheel->is_active)
                                <span class="badge bg-success">Đang hoạt động</span>
                            @else
                                <span class="badge bg-secondary">Không hoạt động</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Ngày tạo</th>
                        <td>{{ $luckyWheel->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Ngày cập nhật</th>
                        <td>{{ $luckyWheel->updated_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Thống kê giải thưởng</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tên giải</th>
                                        <th>Tỷ lệ (%)</th>
                                        <th>Số lượng</th>
                                        <th>Còn lại</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($luckyWheel->prizes as $prize)
                                    <tr>
                                        <td>{{ $prize->name }}</td>
                                        <td>{{ $prize->win_rate }}%</td>
                                        <td>{{ $prize->quantity }}</td>
                                        <td>{{ $prize->remaining }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Chưa có giải thưởng nào</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
