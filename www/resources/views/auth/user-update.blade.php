@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="row">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent py-0">
                            <li class="breadcrumb-item"><a href="{{url('user')}}">จัดการพนักงาน</a></li>
                            <li class="breadcrumb-item active" aria-current="page">แก้ไขข้อมูลพนักงาน</li>
                        </ol>
                    </nav>
                </div>

                @if (count($errors) > 0)
                    @foreach ($errors->all() as $error)

                        <div class="alert alert-danger" role="alert">
                            <span class="oi oi-warning"></span> {{ $error }}
                        </div>
                        <script>
                            $(".alert-danger").fadeOut(2000, function(){
                                $(".alert-danger").fadeOut(500);
                            });
                        </script>

                    @endforeach
                @endif

                <div class="card">
                    <div class="card-header"><h5>{{ __('แก้ไขข้อมูลพนักงาน') }}</h5></div>

                    <div class="card-body">
                        <form method="POST" action="{{ url('user/update') }}" autocomplete="off">
                            @csrf
                            <div class="d-none">
                                <input type="password"/>
                            </div>

                            <div class="form-group row required">
                                <label for="u_name" class="col-md-4 col-form-label text-md-right">{{ __('ชื่อพนักงาน') }}</label>

                                <div class="col-md-6">
                                    <input id="u_name" type="text" class="form-control{{ $errors->has('u_name') ? ' is-invalid' : '' }}" name="u_name" value="{{ isset($user->u_name) ? $user->u_name : '-' }}" required autofocus>

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
                                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $user->email }}">

                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row required">
                                <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('ชื่อผู้ใช้') }}</label>

                                <div class="col-md-6">
                                    <input type="hidden" name="id" value="{{ $user->id }}">
                                    <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ $user->name }}">

                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @else
                                        <span class="invalid-feedback text-muted">ใช้สำหรับเข้าสู่ระบบ</span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('เปลี่ยนรหัสผ่าน') }}</label>

                                <div class="col-md-6">
                                    <button type="button" class="btn btn-outline-primary btn-lg" data-toggle="modal" data-target="#exampleModal">
                                        เปลี่ยนรหัสผ่าน
                                    </button>
                                </div>
                            </div>

                            <div class="form-group row required">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('สิทธิผู้ใช้') }}</label>

                                <div class="col-md-6">
                                    <select id="role_id" class="form-control{{ $errors->has('role_id') ? ' is-invalid' : '' }}" name="role_id" required>
                                        <option value="" selected disabled hidden >กรุณาระบุตำแหน่ง</option>
                                        @foreach ($listRole as $data)
                                            <option value="{{ $data->id }}" {{ $user->roles[0]->id != $data->id  ? 'false' : 'selected' }} >{{ $data->description }}</option>
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
                                            <option value="{{ $data->id }}" {{ $user->branch_id != $data->id  ? 'false' : 'selected' }} >{{ $data->name }}</option>
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
                                        {{ __('ยืนยันการแก้ไข') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <form method="POST" action="{{ url('user/password') }}" autocomplete="off">

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body pt-4">
                    <h2 class="text-center">เปลี่ยนรหัสผ่าน {{$user->name}}</h2>
                    <p class="text-center">ผู้ใช้ {{$user->name}} จะต้องเข้าสู่ระบบใหม่</p>
                        @csrf
                        <input type="hidden" name="id" value="{{ $user->id }}">
                        <div class="form-group justify-content-center row required">
                            <label for="password" class="col-10">{{ __('รหัสผ่านใหม่') }}</label>
                            <div class="col-10">
                                <input id="password" type="password" class="form-control" name="password" autocomplete="new-password" required>
                            </div>
                        </div>
                        <div class="form-group justify-content-center row required">
                            <label for="password-confirm" class="col-10">{{ __('ยืนยันรหัสผ่านใหม่') }}</label>
                            <div class="col-10">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="submit" class="btn btn-primary btn-lg">เปลี่ยนรหัสผ่าน</button>
                        <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">ยกเลิก</button>
                    </div>
            </div>
        </div>
    </div>
    </form>




@endsection
