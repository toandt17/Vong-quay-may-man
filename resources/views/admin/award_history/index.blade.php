@extends('admin.layout.app')

@section('title', 'Lịch sử trúng thưởng')

@section('header', 'Lịch sử trúng thưởng')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Lịch sử trúng thưởng</h5>
            <a href="{{ route('admin.award-histories.export') }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Xuất Excel
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Bộ lọc -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.award-histories.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="lucky_wheel_id" class="form-label">Vòng quay</label>
                        <select class="form-select" id="lucky_wheel_id" name="lucky_wheel_id">
                            <option value="">Tất cả</option>
                            @foreach($luckyWheels as $wheel)
                                <option value="{{ $wheel->id }}" {{ request('lucky_wheel_id') == $wheel->id ? 'selected' : '' }}>
                                    {{ $wheel->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="is_win" class="form-label">Trạng thái</label>
                        <select class="form-select" id="is_win" name="is_win">
                            <option value="">Tất cả</option>
                            <option value="1" {{ request('is_win') === '1' ? 'selected' : '' }}>Trúng thưởng</option>
                            <option value="0" {{ request('is_win') === '0' ? 'selected' : '' }}>Không trúng thưởng</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.award-histories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-sync"></i> Đặt lại
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Người tham gia</th>
                        <th>Số điện thoại</th>
                        <th>Vòng quay</th>
                        <th>Giải thưởng</th>
                        <th>Thời gian quay</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($awardHistories as $history)
                    <tr>
                        <td>{{ $history->id }}</td>
                        <td>{{ $history->participant->name }}</td>
                        <td>{{ $history->participant->phone }}</td>
                        <td>{{ $history->luckyWheel->name }}</td>
                        <td>{{ $history->prize ? $history->prize->name : 'Không trúng thưởng' }}</td>
                        <td>{{ $history->spin_time->format('d/m/Y H:i:s') }}</td>
                        <td>
                            @if($history->is_win)
                                <span class="badge bg-success">Trúng thưởng</span>
                            @else
                                <span class="badge bg-secondary">Không trúng thưởng</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.award-histories.show', $history) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Không có dữ liệu</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $awardHistories->links() }}
        </div>
    </div>
</div>
@endsection
