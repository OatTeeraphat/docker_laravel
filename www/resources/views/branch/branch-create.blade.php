@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">

                <div class="row">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent py-0">
                            <li class="breadcrumb-item"><a href="{{url('branch')}}">จัดการสาขา</a></li>
                            <li class="breadcrumb-item active" aria-current="page">เพิ่มสาขาใหม่</li>
                        </ol>
                    </nav>
                </div>

                <div class="card">
                    <div class="card-header"><h5><strong>เพิ่มสาขาใหม่</strong></h5></div>

                    <div class="card-body">
                        <form method="POST" action="{{ url('branch/create') }}" autocomplete="off">
                            @csrf

                            <div class="form-group row required">
                                <label for="name" class="col-md-4 col-form-label text-md-right">ชื่อสาขา</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus>

                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row" id="address_group">
                                <label for="address_" class="col-md-4 col-form-label text-md-right">ที่อยู่</label>
                                <div class="col-md-6">
                                    <textarea id="address_" type="text" class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }} text-area-primary" name="address" autofocus rows="2">{{ old('address') }}</textarea>
                                    <span id="address_count" class="address_count"></span>
                                    @if ($errors->has('address'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                                    @else
                                        <span class="invalid-feedback text-muted">แสดงผลในใบรับงานไม่เกิน 50 ตัวอักษร</span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row required">
                                <label for="time_open" class="col-md-4 col-form-label text-md-right">วันทำการ</label>

                                <div class="col-md-6">
                                    <input id="date_open" type="text" class="form-control{{ $errors->has('date_open') ? ' is-invalid' : '' }}" name="date_open"
                                           value="{{ old('date_open') }}"required>
                                    @if ($errors->has('date_open'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('date_open') }}</strong>
                                    </span>
                                    @else
                                        <span class="invalid-feedback text-muted">รูปแบบ จันทร์-เสาร์ หรือ ทุกวัน</span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row required">
                                <label for="time_open" class="col-md-4 col-form-label text-md-right">เวลาทำการ</label>

                                <div class="col-md-6">
                                    <input id="time_open" type="text" class="form-control{{ $errors->has('time_open') ? ' is-invalid' : '' }}"
                                           value="{{ old('time_open') }}" name="time_open" required>
                                    @if ($errors->has('time_open'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('time_open') }}</strong>
                                    </span>
                                    @else
                                        <span class="invalid-feedback text-muted">รูปแบบ 00:00-00:00น.</span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row required">
                                <label for="phone" class="col-md-4 col-form-label text-md-right">หมายเลขโทรศัพท์</label>
                                <div class="col-md-6">
                                    <input id="phone" type="text" maxlength="12" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ old('phone') }}" autofocus>
                                    <span class="invalid-feedback">
                                        <strong id="phone_error">@if ($errors->has('phone')){{ $errors->first('phone')}}@endif</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary btn-lg mt-2">
                                        {{ __('ยืนยัน เพิ่มสาขาใหม่') }}
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

            $(function () {
                $("#address_").trigger('keyup')
            })
            $("#address_").keyup(function(){
                $("#address_count").text($(this).val().length + ' อักษร');
            });


        })
    </script>

@stop