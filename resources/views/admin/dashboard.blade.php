@extends('admin.layout.master')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Dashboard</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <h5><i class="icon fas fa-check"></i> Xin chào!</h5>
                        Bạn đã đăng nhập thành công với tài khoản <strong>{{ Auth::user()->name }}</strong>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>Vòng Quay</h3>
                                    <p>Quản lý vòng quay may mắn</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-sync"></i>
                                </div>
                                <a href="{{ route('admin.lucky-wheels.index') }}" class="small-box-footer">
                                    Xem chi tiết <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>Giải Thưởng</h3>
                                    <p>Quản lý giải thưởng</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-gift"></i>
                                </div>
                                <a href="{{ route('admin.lucky-wheels.index') }}" class="small-box-footer">
                                    Xem chi tiết <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>Lịch Sử</h3>
                                    <p>Lịch sử trúng thưởng</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-history"></i>
                                </div>
                                <a href="{{ route('admin.award-histories.index') }}" class="small-box-footer">
                                    Xem chi tiết <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
