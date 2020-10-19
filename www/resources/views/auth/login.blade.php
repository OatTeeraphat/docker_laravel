@extends('layouts.app')
@section('content')
    @php
        $checkPin = Session::get('pin')[0];
    @endphp
<div class="center-all">
<div class="container justify-content-center">

    <div class="row justify-content-center">
        <div class="col-11 col-lg-4 col-sm-10 text-center login-container">
            <img src="{{ url('/') }}/public/images/jewerly-t.png" class="logo-main" alt="">
            <h3>เข้าสู่ระบบ</h3><br>

            <form method="POST" action="{{ url('login') }}" id="login-form"  autocomplete="off">
                @csrf
                <input type="password" class="d-none" name="password-fuck-off"  />
                <div class="form-group row" id="name-container">
                    <div class="col-12">
                        <input id="email" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" placeholder="ชื่อผู้ใช้" value="{{ old('email') }}"  required autofocus>
                    </div>
                </div>

                <div class="form-group row" id="pass-container">
                    <div class="col-12">
                        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="รหัสผ่าน"  autocomplete="new-password" required>
                    </div>

                    <div class="col-12">
                        @if ($errors->has('email'))
                            <span class="invalid-feedback">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                        @if ($errors->has('password'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                        @if (isset($checkPin))
                            <span class="invalid-feedback">
                            <strong>{{ $checkPin }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row mb-0 justify-content-center" id="button-container">
                    <div class="col-12">
                        <button type="button" id="btn-login" class="btn btn-primary btn-lg btn-block">
                            {{ __('เข้าสู่ระบบ') }}
                        </button>

                        {{--<a class="btn btn-link" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>--}}
                    </div>
                </div>

                <div class="form-group row" id="pin-box">
                    <div class="col-12">
                        <label for="pin">โปรดระบุรหัส PIN 4 หลัก</label>
                        <input id="pin" type="password" class="form-control pin-input mb-3" name="pin" pattern="[0-9]*" inputmode="numeric" placeholder="PIN" autocomplete="new-password">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            {{ __('ยืนยันรหัส PIN') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
</div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function($) {
            $('#btn-login').on('click',function () {
                let name = $('#email').val();
                let password = $('#password').val();
                let oldPin ="{{Cookie::get('PIN') !== null ? Cookie::get('PIN') : 'null'}}";
                //console.log(oldPin);
                if (name.length > 0) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ url('checkHasCookiePin') }}?data="+name+"!"+oldPin,
                        dataType: 'JSON',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (data) {
                            console.log(data)
                            if (data !== true) {
                                $('#name-container, #pass-container, #button-container').addClass('login-name-left')
                                $('#pin-box, #num-container').addClass('login-login-left').delay('300')
                            } else {
                                $('#login-form').submit();
                            }
                        }
                    });

                }
            })

            $('#pin').on('keyup',function () {
                let pin = $(this).val();
                if (pin.length <= 4) {
                    return true
                } else {
                    return $(this).val(pin.slice(0,4));
                }
            });
            
        })
    </script>
@stop
