<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="-1" />
    <link rel="shortcut icon" href="{{ asset('public/favicon.ico')}}" type="image/x-icon">
    <link rel="icon" href="{{ asset('public/favicon.ico')}} " type="image/x-icon">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>JewelryClinic</title>

    <!-- Scripts -->
    <script src="{{ asset('public/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('public/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('public/js/bootstrap-datepicker-custom.js') }}"></script>
    <script src="{{ asset('public/js/bootstrap-datepicker.th.min.js') }}" charset="UTF-8"></script>
    <script src="{{ asset('public/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('public/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('public/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('public/js/dataTables.buttons.print.min.js') }}"></script>
    <script src="{{ asset('public/js/dataTables.buttons.html5.min.js') }}"></script>
    <script src="{{ asset('public/js/jquery-sortable.js') }}"></script>
    <script src="{{ asset('public/js/imageuploader.js') }}" defer></script>
    <script src="{{ asset('public/js/imageuploader.theme.js') }}" defer></script>
    <script src="{{ asset('public/js/piexif.js') }}" defer></script>
    <script src="{{ asset('public/js/FileSaver.js') }}"></script>
    <script src="{{ asset('public/js/xlsx.core.min.js') }}"></script>
    <script src="{{ asset('public/js/jhxlsx.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/async/3.1.0/async.min.js"></script>


    <script src="{{ asset('public/js/app.js') }}" defer></script>



    <!-- Fonts -->
    <link href="{{ asset('public/fonts/fonts.css') }}" rel="stylesheet" type="text/css">


    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('public/css/iconic-bootstrap.css') }}">
    <link href="{{ asset('public/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/buttons.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/styles.imageuploader.css') }}?v=0.4" rel="stylesheet">
    <link href="{{ asset('public/css/app.css') }}?v=0.573" rel="stylesheet">
    <link href="{{ asset('public/css/main.css') }}?v=0.573" rel="stylesheet">
</head>
<body>
        <div id="app" class="pt-5 mt-2 clear-print">

        @guest
        @else
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel fixed-top">
            <div class="container">
                <a class="navbar-brand" href="{{ url('bill') }}">
                    <img src="{{ asset('public/images/jewerly-t.png') }}"alt="" style="width: 25px; height: 25px">
                    Jewelry Clinic
                    <span class="badge badge-primary d-none d-sm-inline-block ml-2 mr-3"> {{ Auth::user()->branch->name }}</span>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto d-md-none ">
                        <li class="nav-item"><a class="nav-link text-primary font-weight-bold" href="{{ url('recent') }}">{{ Auth::user()->name }} - สาขา{{ Auth::user()->branch->name }} </a></li>
                        <div class="dropdown-divider mt-2 mb-1"></div>
                        @role('admin')
                        <li class="nav-item"><a class="nav-link" href="{{ url('user') }}">ตั้งค่าพนักงาน </a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('branch') }}">ตั้งค่าสาขา</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('setting') }}">ตั้งค่าข้อมูลบิล</a></li>
                        <div class="dropdown-divider mt-2 mb-1"></div>
                        @endrole
                        <li class="nav-item"><a class="nav-link" href="{{ url('customer') }}">จัดการลูกค้า </a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('export') }}">ออกรายงาน </a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('report') }}">จัดการฐานข้อมูล </a></li>
                        <div class="dropdown-divider mt-2 mb-1"></div>

                        <li class="nav-item"><a class="my-2 btn btn-outline-primary"
                           href="{{ route('logout') }}"
                           onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            <h5 class="mb-1 mt-0">ออกจากระบบ</h5>
                        </a></li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle d-none d-md-block" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ Auth::user()->u_name }}
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url('bill') }}">บิลรับงาน </a>
                                    <a class="dropdown-item" href="{{ url('recent') }}">ดูบิลเก่า </a>
                                    <a class="dropdown-item" href="{{ url('summary') }}">สรุปรายวัน </a>
                                    <div class="dropdown-divider"></div>
                                @role('admin')
                                    <a class="dropdown-item" href="{{ url('user') }}">ตั้งค่าพนักงาน </a>
                                    <a class="dropdown-item" href="{{ url('branch') }}">ตั้งค่าสาขา</a>
                                    <a class="dropdown-item" href="{{ url('setting') }}">ตั้งค่าข้อมูลบิล</a>
                                    <div class="dropdown-divider"></div>
                                @endrole
                                    <a class="dropdown-item" href="{{ url('customer') }}">จัดการลูกค้า </a>
                                    <a class="dropdown-item" href="{{ url('export') }}">จัดการฐานข้อมูล </a>
                                    <a class="dropdown-item" href="{{ url('report') }}">ออกรายงาน </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item"
                                        href="{{ route('logout') }}"
                                        onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                        <p class=" font-weight-bold m-0">ออกจากระบบ</p>
                                    </a>
                                    <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </div>

                    </ul>
                </div>
            </div>
        </nav>
        @endguest
        <main class="py-3 py-md-4">
            {{--@if(url()->current() != url('/'))--}}
                {{--<div class="timer text-center">--}}
                    {{--<p><i class="oi oi-timer mr-1"></i>ทำรายการ</p>--}}
                    {{--<h1 id="timerMsg">15:00</h1>--}}
                    {{--<a id="logout"--}}
                       {{--href="{{ route('logout') }}"--}}
                       {{--onclick="event.preventDefault();document.getElementById('logout-form').submit();--}}
                               {{--">ออกจากระบบ</a>--}}
                {{--</div>--}}
            {{--@endif--}}
            @yield('content')
        </main>
    </div>
        <script>
            // $(document).ready(function($) {
            //     $('.table-responsive').insertBefore($('.dataTable')).append($('.dataTable'));
            //     countDown();
            // });
            //
            // function countDown(e) {
            //
            //     var timer2 = "15:00";
            //     var interval = setInterval(function(e) {
            //         var timer = timer2.split(':');
            //         //by parsing integer, I avoid all extra string processing
            //         var minutes = parseInt(timer[0], 10);
            //         var seconds = parseInt(timer[1], 10);
            //         --seconds;
            //         minutes = (seconds < 0) ? --minutes : minutes;
            //         seconds = (seconds < 0) ? 59 : seconds;
            //         seconds = (seconds < 10) ? '0' + seconds : seconds;
            //         //minutes = (minutes < 10) ?  minutes : minutes;
            //         $('#timerMsg').html(minutes + ':' + seconds);
            //         if (minutes < 0) clearInterval(interval);
            //         //check if both minutes and seconds are 0
            //         if ((seconds <= 0) && (minutes <= 1)){
            //             clearInterval(interval);
            //             document.getElementById('logout-form').submit();
            //         }
            //         timer2 = minutes + ':' + seconds;
            //     }, 1000);
            //
            // }

        </script>
        @yield('scripts')


        <script src="{{ asset('public/js/main.js') }}" defer></script>
</body>
</html>
