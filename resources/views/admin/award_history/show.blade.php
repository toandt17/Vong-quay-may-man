@extends('admin.layout.app')

@section('title', 'Chi tiết lịch sử trúng thưởng')

@section('header', 'Chi tiết lịch sử trúng thưởng')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Chi tiết lịch sử trúng thưởng</h5>
            <a href="{{ route('admin.award-histories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Thông tin lượt quay</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 30%">ID</th>
                                <td>{{ $awardHistory->id }}</td>
                            </tr>
                            <tr>
                                <th>Vòng quay</th>
                                <td>{{ $awardHistory->luckyWheel->name }}</td>
                            </tr>
                            <tr>
                                <th>Thời gian quay</th>
                                <td>{{ $awardHistory->spin_time->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Kết quả</th>
                                <td>
                                    @if($awardHistory->is_win)
                                        <span class="badge bg-success">Trúng thưởng</span>
                                    @else
                                        <span class="badge bg-secondary">Không trúng thưởng</span>
                                    @endif
                                </td>
                            </tr>
                            @if($awardHistory->is_win && $awardHistory->prize)
                            <tr>
                                <th>Giải thưởng</th>
                                <td>{{ $awardHistory->prize->name }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($awardHistory->is_win && $awardHistory->prize && $awardHistory->prize->image)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Hình ảnh giải thưởng</h5>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ asset('storage/' . $awardHistory->prize->image) }}" alt="{{ $awardHistory->prize->name }}" class="img-fluid" style="max-height: 200px;">
                    </div>
                </div>
                @endif
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Thông tin người tham gia</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 30%">Họ tên</th>
                                <td>{{ $awardHistory->participant->name }}</td>
                            </tr>
                            <tr>
                                <th>Số điện thoại</th>
                                <td>{{ $awardHistory->participant->phone }}</td>
                            </tr>
                            <tr>
                                <th>Tỉnh/Thành phố</th>
                                <td>{{ $awardHistory->participant->province }}</td>
                            </tr>
                            <tr>
                                <th>Quận/Huyện</th>
                                <td>{{ $awardHistory->participant->district }}</td>
                            </tr>
                            <tr>
                                <th>Phường/Xã</th>
                                <td>{{ $awardHistory->participant->ward }}</td>
                            </tr>
                            <tr>
                                <th>Địa chỉ cụ thể</th>
                                <td>{{ $awardHistory->participant->address }}</td>
                            </tr>
                            <tr>
                                <th>Là nông dân</th>
                                <td>{{ $awardHistory->participant->is_farmer ? 'Có' : 'Không' }}</td>
                            </tr>
                            @if($awardHistory->participant->is_farmer)
                            <tr>
                                <th>Giống lúa</th>
                                <td>{{ $awardHistory->participant->rice_variety ?: 'Không có thông tin' }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Sản phẩm đã sử dụng</th>
                                <td>{{ $awardHistory->participant->used_products ?: 'Không có thông tin' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
