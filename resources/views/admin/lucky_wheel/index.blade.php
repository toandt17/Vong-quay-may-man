@extends('admin.layout.app')

@section('title', 'Danh sách vòng quay may mắn')

@section('header', 'Danh sách vòng quay may mắn')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Danh sách vòng quay may mắn</h5>
            <a href="{{ route('admin.lucky-wheels.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm mới
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Mô tả</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($luckyWheels as $luckyWheel)
                    <tr>
                        <td>{{ $luckyWheel->id }}</td>
                        <td>{{ $luckyWheel->name }}</td>
                        <td>{{ Str::limit($luckyWheel->description, 50) }}</td>
                        <td>
                            @if($luckyWheel->is_active)
                                <span class="badge bg-success">Đang hoạt động</span>
                            @else
                                <span class="badge bg-secondary">Không hoạt động</span>
                            @endif
                        </td>
                        <td>{{ $luckyWheel->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.lucky-wheels.show', $luckyWheel) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.lucky-wheels.edit', $luckyWheel) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.lucky-wheels.prizes.index', $luckyWheel) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-gift"></i>
                                </a>
                                <form action="{{ route('admin.lucky-wheels.destroy', $luckyWheel) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vòng quay này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Không có dữ liệu</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $luckyWheels->links() }}
        </div>
    </div>
</div>
@endsection
