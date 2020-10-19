@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="row">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent py-0">
                            <li class="breadcrumb-item active" aria-current="page">จัดการสาขา</li>
                        </ol>
                    </nav>
                </div>
                @if(Session::has('success'))
                    <div class="alert alert-success" role="alert">
                        <span class="oi oi-check"></span> {{ Session::get('success') }}
                    </div>
                    <script>
                        $(".alert-success").delay('3000').fadeOut(300);
                    </script>
                @endif
                <div class="card">
                    <div class="card-header"><h5><strong>จัดการสาขา</strong></h5></div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-12">
                                <a class="btn btn-primary btn-lg" href="{{ url('branch/create') }}" role="button">เพิ่มสาขาใหม่</a>
                                <hr>
                            </div>
                        </div>
                        <div class="table-responsive"></div>
                        <input type="password" class="d-none" />
                        <table id="Table" class="table table-striped table-bordered table-report">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>ชื่อ</th>
                                <th>ที่อยู่</th>
                                <th>เวลาทำการ</th>
                                <th>โทร</th>
                                <th scope="col" width="40">ช่าง</th>
                                <th scope="col" width="40">แก้ไข</th>
                                <th scope="col" width="40">ลบ</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($list as $i => $item)
                                <tr id="row-{{ $i+1 }}">
                                    <td scope="row">{{ $item->id }}</td>
                                    <td class="name">{{ !is_null($item->name) ? $item->name : '-' }}</td>
                                    <td>{{ !is_null($item->address) ? $item->address : '-' }}</td>
                                    <td>{{ !is_null($item->time_open ) ? $item->time_open : '-' }}
                                        <br>{{ !is_null( $item->date_open ) ? $item->date_open : '-' }}</td>
                                    <td>{{ !is_null($item->phone) ? $item->phone : '-' }}</td>
                                    <td><a href="{{ url('branch/craft?id='.$item->id) }}" class="badge badge-primary badge-icon"><span class="oi oi-person"></span></a></td>
                                    <td><a href="{{ url('branch/update?id='.$item->id) }}" class="badge badge-primary badge-icon"><span class="oi oi-pencil"></span></a></td>
                                    <td><a href="" data-id='{{ $item->id }}' data-path='{{ url('branch/delete?id=') }}' class="badge badge-danger badge-icon" data-toggle="modal" data-target="#exampleModal" id="{{ $i+1 }}"><span class="oi oi-trash" id="{{ $i+1 }}"></span></a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
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
                    <h2 class="text-center">ต้องการลบ สาขา<span id="name"></span></h2>
                    <p class="text-center">ข้อมูลสาขา<span id="name"></span>จะไม่ถูกแสดงในระบบ</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">ยกเลิก</button>
                    <a class="btn btn-danger btn-lg" role="button">ยืนยันลบ</a>
                </div>
            </div>
        </div>
    </div>
    <script language="javascript" type="text/javascript">

    </script>
    <div class="box"></div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function($) {

            $('#Table').DataTable({
                responsive: true,
                columnDefs:[
                    {targets: [-1, -2, -3],orderable: false},
                    {targets: [1, 2, 3],width: '150px'}
                ],
            });

            $('a.badge.badge-danger,span.oi-trash').click(function (e) {
                let selector ='#row-' + e.target.id + ' td.name';
                var data = $(this).data();
                var str = $(selector).text();
                $( "span#name" ).html( str );
                $( ".modal-footer a.btn-danger" ).attr( 'href', data.path+data.id );
            });



        } );

    </script>
@stop



