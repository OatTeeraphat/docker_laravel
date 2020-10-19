@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="row">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent py-0">
                            <li class="breadcrumb-item"><a href="{{url('branch')}}">จัดการสาขา</a></li>
                            <li class="breadcrumb-item active" aria-current="page">จัดการช่างทอง</li>
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
                    <div class="card-header"><h5><strong>จัดการช่างทอง สาขา{{$branch[0]->name}}</strong></h5></div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modalPay" role="button">เพิ่มช่างทอง</button>
                                <hr>
                            </div>
                        </div>
                        <div class="table-responsive"></div>
                        <input type="password" class="d-none" />
                        <table id="Table" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th width="40">#</th>
                                <th scope="col">ชื่อช่างทอง</th>
                                <th scope="col" width="40">ลบ</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($craft as $i => $item)
                                <tr id="row-{{ $i+1 }}">
                                    <td scope="row">{{ $item->id }}</td>
                                    <td class="name">{{ !is_null($item->name) ? $item->name : '-' }}</td>
                                    <td><a href="" data-id='{{ $item->id }}' data-path='{{ url('branch/craft/delete?id=') }}' class="badge badge-danger badge-icon" data-toggle="modal" data-target="#exampleModal" id="{{ $i+1 }}"><span class="oi oi-trash" id="{{ $i+1 }}"></span></a></td>
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
                    <p class="text-center">รายการที่เกี่ยวข้องกับสาขา<span id="name"></span> จะถูกลบด้วย</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                    <a class="btn btn-danger"  role="button">ยืนยันลบ</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPay" tabindex="-1" role="dialog" aria-labelledby="modalPay" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <form method="POST" action="{{ url('branch/craft') }}" autocomplete="off">
            @csrf
            <div class="modal-dialog modal-pay" role="document">
                <div class="modal-content">
                    <div class="modal-body pt-4">
                        <div class="row justify-content-center">
                            <div class="col-11 col-md-10">
                                <h2 class="text-center mb-1">เพิ่มช่างทอง</h2>
                                <p class="text-center">ใน สาขา{{$branch[0]->name}}</p>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-12 col-md-10">
                                <div class="form-group row required">
                                    <div class="col-12">
                                        <label for="gold">ชื่อช่างทอง</label>
                                        <div class="input-group input-group-lg">
                                            <input type="text" class="form-control" name="name" id="craft">
                                            <input type="text" class="form-control d-none" name="id" value="{{$branch[0]->id}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="submit" class="btn btn-primary btn-lg mr-2">ยืนยัน เพิ่มช่างทอง</button>
                        <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">ยกเลิก</button>
                    </div>
                </div>
            </div>
        </form>
    </div>


@endsection
@section('scripts')
    <script>
        $(document).ready(function($) {

            $('#Table').DataTable({
                responsive: true,
                columnDefs:[
                    {targets: [-1],orderable: false},
                ],
            });

            $('a.badge.badge-danger,span.oi-trash').click(function (e) {
                let selector ='#row-' + e.target.id + ' td.name';
                let data = $(this).data();
                var str = $(selector).text();
                $( "span#name" ).html( str );
                $( ".modal-footer a.btn-danger" ).attr( 'href', data.path+data.id );
            });



        } );

    </script>
@stop



