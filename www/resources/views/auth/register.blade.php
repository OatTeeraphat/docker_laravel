@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="row">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent py-0">
                        <li class="breadcrumb-item"><a href="{{url('user')}}">จัดการพนักงาน</a></li>
                        <li class="breadcrumb-item active" aria-current="page">เพิ่มพนักงานใหม่</li>
                    </ol>
                </nav>
            </div>
            <div class="card">
                <div class="card-header"><h5><strong>เพิ่มพนักงาน</strong></h5></div>

                <div class="card-body">
                        <form method="POST" action="{{ url('register') }}" autocomplete="off">

                        @csrf

                        <div class="form-group row required">
                            <label for="u_name" class="col-md-4 col-form-label text-md-right">{{ __('ชื่อพนักงาน') }}</label>

                            <div class="col-md-6">
                                <input id="u_name" type="text" class="form-control{{ $errors->has('u_name') ? ' is-invalid' : '' }}" name="u_name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('u_name'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('u_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('อีเมลล์') }}</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label for="name" class="col-md-4 mt-3 col-form-label text-md-right">{{ __('ชื่อผู้ใช้') }}</label>

                            <div class="col-md-6 mt-3">
                                <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @else
                                    <span class="invalid-feedback text-muted">ใช้สำหรับเข้าสู่ระบบ</span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('รหัสผ่าน') }}</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" autocomplete="new-password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @else
                                    <span class="invalid-feedback text-muted">ไม่น้อยกว่า 6 ตัวอักษร</span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label for="password-confirm" class="col-md-4 mb-2 col-form-label text-md-right">{{ __('ยืนยันรหัสผ่าน') }}</label>

                            <div class="col-md-6 mb-2">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="off" required>
                                <span class="invalid-feedback text-muted">พิมพ์รหัสผ่านซ้ำ</span>
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label for="pin" class="col-md-4 mb-2 col-form-label text-md-right">{{ __('รหัส PIN') }}</label>
                            <div class="col-md-6 mb-2">
                                <div class="input-group input-group-lg">
                                    <input type="text" class="form-control not-require text-center" name="pin" pattern="[0-9]*" inputmode="numeric" id="pin">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary" type="button" id="button-addon1">สุ่มรหัส</button>
                                    </div>
                                </div>
                                <span class="invalid-feedback text-muted">รหัส PIN 4 หลัก</span>
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('สิทธิผู้ใช้') }}</label>

                            <div class="col-md-6">

                                <select id="role_id" class="form-control{{ $errors->has('role_id') ? ' is-invalid' : '' }}" name="role_id" required>
                                    <option value="" selected disabled hidden >กรุณาระบุตำแหน่ง</option>
                                    @foreach ($listRole as $data)
                                        <option value="{{ $data->id }}" >{{ $data->description }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('role_id'))
                                    <span class="invalid-feedback">
                                        <strong>กรุณาระบุสาขา</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('สาขา') }}</label>
                            <div class="col-md-6">
                                <select id="branch_id" class="form-control{{ $errors->has('branch_id') ? ' is-invalid' : '' }}" name="branch_id" required>
                                        <option value="" selected disabled hidden >กรุณาระบุสาขา</option>
                                    @foreach ($listBranch as $data)
                                        <option value="{{ $data->id }}" >{{ $data->name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('branch_id'))
                                    <span class="invalid-feedback">
                                        <strong>กรุณาระบุสาขา</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4 mt-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    {{ __('ยืนยัน เพิ่มพนักงาน') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function($) {
            $('#pin').on('keyup',function () {
                let pin = $(this).val();
                if (pin.length <= 4) {
                    return true
                } else {
                    return $(this).val(pin.slice(0,4));
                }
            });
            $('#button-addon1').on('click',function () {
                var val = Math.floor(1000 + Math.random() * 9000);
                $('#pin').val(val);
            })

        })
    </script>
@stop
