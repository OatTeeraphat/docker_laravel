@extends('layouts.app')
{!! config(['app.title' => 'Manage Users']) !!}
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">

                <div class="row">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent py-0">
                            <li class="breadcrumb-item active" aria-current="page">จัดการพนักงาน</li>
                        </ol>
                    </nav>
                </div>
                @if(Session::has('success'))
                    <div class="alert alert-success" role="alert">
                        <span class="oi oi-check"></span> {{ Session::get('success') }}
                    </div>
                    <script>
                        $(".alert-success").fadeOut(3000, function(){
                            $(".alert-success").fadeOut(500);
                        });
                    </script>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h5><strong>จัดการพนักงาน</strong></h5>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <a class="btn btn-primary btn-lg" href="{{ url('register') }}" role="button">เพิ่มพนักงาน</a>
                                <hr>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive"></div>
                                <input type="password" class="d-none" />
                                <table id="Table" class="table table-striped table-bordered table-report">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">ชื่อผู้ใช้</th>
                                        <th scope="col">ชื่อพนักงาน</th>
                                        <th scope="col">สาขา</th>
                                        <th scope="col">สิทธิ์ใช้งาน</th>
                                        <th scope="col" width="40">PIN</th>
                                        <th scope="col" width="40">แก้ไข</th>
                                        <th scope="col" width="40">ลบ</th>
                                        <th scope="col" width="40">สถานะ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($listUsers as $i => $user)
                                        <tr id="row-{{ $i+1 }}">
                                            <th scope="row">{{ $i+1 }}</th>
                                            <td class="name">{{ $user->name }}</td>
                                            <td>{{ $user->u_name }}</td>
                                            <td>{{ $user->branch->name }}</td>
                                            <td>{{ $user->roles[0]->description }}</td>
                                            <td><a href="" id="{{ $i+1 }}" class="badge badge-primary badge-icon badge-pin" data-toggle="modal" data-target="#modalPin" data-id='{{ $user->id }}' data-pin="{{ $user->pin }}">
                                                    <span class="oi oi-key" id="{{ $i+1 }}"></span></a>
                                            </td>
                                            <td><a href="{{ url('user/update?id='.$user->id) }}" class="badge badge-primary badge-icon">
                                                    <span class="oi oi-pencil"></span></a>
                                            </td>

                                            <td><a href="" id="{{ $i+1 }}" class="badge badge-danger badge-icon" data-toggle="modal" data-target="#exampleModal"  data-id='{{ $user->id }}' data-path='{{ url('user/delete?id=') }}'>
                                                    <span class="oi oi-trash" id="{{ $i+1 }}"></span></a>
                                            </td>
                                            <td><a href="" class="badge {{ $user->status == 0 ? 'badge-secondary' : 'badge-success' }} badge-icon not-pointer">
                                                    <span class="oi oi-power-standby"></span></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="d-none">
                <input type="password"/>
            </div>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body pt-4">
                        <h2 class="text-center">ต้องการลบ ผู้ใช้<span id="name"></span></h2>
                        <p class="text-center">รายการที่ผู้ใช้<span id="name"></span> สร้างจะยังคงอยู่</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">ยกเลิก</button>
                        <a class="btn btn-danger btn-lg" role="button">ยืนยันลบ</a>
                    </div>
                </div>
            </div>
        </div>

    <!-- Modal -->
    <div class="modal fade" id="modalPin" tabindex="-1" role="dialog" aria-labelledby="modalPin" aria-hidden="true">
        <div class="d-none">
            <input type="password"/>
        </div>
        <div class="modal-dialog"  role="document">
            <form action="{{ url('user/setPin') }}" method="POST" id="reset-pin" autocomplete="off">
                @csrf
                <div class="modal-content">
                    <div class="modal-body pt-4">
                        <h2 class="text-center">รหัส PIN ของ<span id="namee"></span></h2>
                        <p class="text-center">ผู้ใช้งานจะต้องเข้าสู่ระบบใหม่เมื่อ PIN ถูกแก้ไข</p>
                        <div class="row justify-content-center ">
                            <div class="col-8">
                                <div class="form-group row required">
                                    <div class="col-12">
                                        <div class="input-group input-group-lg">
                                            <input type="text" class="form-control not-require text-center readonly-w" name="pin" readonly pattern="[0-9]*" inputmode="numeric" id="pin">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button" id="button-addon1" disabled>สุ่มรหัส</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center my-1">
                            <div class="custom-control custom-checkbox mr-sm-2">
                                <input type="checkbox" class="custom-control-input" id="customControlAutosizing">
                                <label class="custom-control-label" for="customControlAutosizing">แก้ไขรหัส PIN 4 หลัก</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="submit" class="btn btn-primary btn-lg" id='btn-conferm' disabled>เปลี่ยน PIN</button>
                        <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">ยกเลิก</button>
                    </div>
                    <input type="text" class="d-none" name="id" id="pin_uid" />
                    <input type="password" class="d-none" name="password-fuck-off"  />
                </div>
            </form>
        </div>
    </div>

@section('scripts')
    <script>
        $(document).ready(function($) {

            $('a.badge.badge-danger,span.oi-trash').click(function (e) {
                let selector ='#row-' + e.target.id + ' td.name';
                var data = $(this).data();
                var str = $(selector).text();
                $( "span#name" ).html( str );
                $( ".modal-footer a.btn-danger" ).attr( 'href', data.path+data.id );
            });

            $('a.badge.badge-pin,span.oi-key').click(function (e) {
                let selector ='#row-' + e.target.id + ' td.name';
                var data = $(this).data();
                var str = $(selector).text();
                $('#pin').val(data.pin);
                $('#pin_uid').val(data.id);
                $( "span#namee" ).html( str );
            });

            $('#Table').DataTable({
                responsive: true,
                columnDefs:[{
                    targets: [-1, -2, -3,-4],
                    orderable: false,
                }],
            });

            $('.dataTables_filter input[type="search"]').bind().attr('name','shit')

            $('#customControlAutosizing').on('change',function () {
                if ($(this).get(0).checked){
                    $('#button-addon1').prop('disabled',false)
                    $('#pin').prop('readonly',false)
                    $('#pin').keyup();
                } else {
                    $('#button-addon1').prop('disabled',true)
                    $('#pin').prop('readonly',true)
                }

            })
            
            $('#button-addon1').on('click',function () {
                var val = Math.floor(1000 + Math.random() * 9000);
                $('#pin').val(val);
                $('#btn-conferm').prop('disabled',false)
            })

            $('#pin').on('keyup',function () {
                let pin = $(this).val();
                if (pin.length === 4) {
                    $('#btn-conferm').prop('disabled',false)
                } else if (pin.length > 4) {
                    $('#btn-conferm').prop('disabled',false)
                    $(this).val(pin.slice(0,4));
                }  else {
                    $('#btn-conferm').prop('disabled',true)
                }
            });

        });
    </script>
@stop

@endsection