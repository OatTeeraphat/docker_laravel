@extends('layouts.app')
{!! config(['app.title' => 'Manage Users']) !!}
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
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
                        <h5>ตั้งค่ารายการส่วนประกอบ</h5>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <a class="btn btn-outline-primary mr-2" id="btn-save-table" href="javascript:void(0)" role="button">แก้ไขหัวตาราง</a>
                                <hr>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-12">
                                <input type="password" class="d-none" />
                                <div class="table-responsive">
                                    <div class="table-sort-container">
                                        <div class="table-sort-amulet" >
                                            <table class="table table-sort table-bordered sorted_table">
                                                <thead class="sorted_head">
                                                <tr>
                                                    <th class="disabled">รายการ</th>
                                                    <th class="disabled min-width-inject">ราคา</th>
                                                </tr>
                                                </thead>
                                                <tbody class="sorted_body">
                                                @foreach ($material as $i => $m)
                                                <tr>
                                                    <th class="disabled" data-job="{{$m->id}}">
                                                        <a href=""
                                                           data-id='{{$m->id}}'
                                                           data-name='{{$m->name}}'
                                                           data-path='{{ url('material/delete?id=') }}'
                                                           class="badge badge-danger mr-1"
                                                           data-toggle="modal"
                                                           data-target="#deleteModal" id="{{ $i+1 }}"
                                                        >
                                                            <span class="oi oi-trash" id="{{ $i+1 }}"></span>
                                                        </a>
                                                        {{$m->name}}
                                                    </th>
                                                    <td></td>
                                                </tr>
                                                @endforeach
                                                <tr class="static">
                                                    <td style="height: 45px; padding: 10px;">
                                                        <a href=""
                                                           class="badge btn-primary btn btn-setting-btm btn-setting mr-1"
                                                           aria-disabled="true"
                                                           role="button"
                                                           data-hr="job"
                                                           data-path="{{ url('material/addMaterial') }}"
                                                           data-toggle="modal"
                                                           data-target="#exampleModal"
                                                        >
                                                            <span class="oi oi-plus"></span>
                                                        </a>
                                                    </td>
                                                    <td colspan="1" style="height: 45px; padding: 10px;"></td>

                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <form id="form-update-table" action="{{ url('material/order') }}" method="POST">
        @csrf
        <input name="data" type="hidden" id="update-table" class="d-none">
    </form>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">เพิ่มตัวเลือก <span id="type"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-add-table" method="POST" >
                        @csrf
                        <div class="form-group row required">
                            <label for="name" class="col-md-3 col-form-label text-md-right">ชื่อ</label>
                            <div class="col-md-7">
                                <input id="name" type="text" class="form-control" name="name" value="" required autofocus>
                            </div>
                        </div>
                        <div class="form-group row required">
                            <label for="unit" class="col-md-3 col-form-label text-md-right">หน่วยวัด</label>
                            <div class="col-md-7">
                                <input id="unit" type="text" class="form-control" name="unit" value="" required autofocus>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="submit-add"
                            onclick="event.preventDefault();
                        document.getElementById('form-add-table').submit();"
                    >+ เพิ่มตัวเลือก</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal-Delete -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="d-none">
            <input type="password"/>
        </div>
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-body">

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="">
                        <h3>ต้องการลบ <span id="name"></span> ใช่หรือไม่</h3>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                    <a class="btn btn-danger"  role="button">ยืนยันลบ</a>
                </div>
            </div>
        </div>
    </div>



@section('scripts')

    <script>
        $(document).ready(function($) {
            var oldIndex;
            $('.sorted_table').sortable({
                containerSelector: 'table',
                itemPath: '> tbody',
                itemSelector: 'tr:not(.static)',
                placeholder: '<tr class="placeholder"/>',
                onDragStart: function($item, container, _super) {
                    oldIndex = $item.index();
                    $item.children().width(300)
                    _super($item, container);
                },
            });

            $('.sorted_head tr').sortable({
                containerSelector: 'tr',
                itemSelector: 'th:not(.static)',
                placeholder: '<th class="placeholder"/>',
                vertical: false,
                onDragStart: function($item, container, _super) {
                    oldIndex = $item.index();
                    $item.appendTo($item.parent());
                    _super($item, container);
                },
                onDrop: function($item, container, _super,) {
                    var field,
                        newIndex = $item.index();

                    if(newIndex != oldIndex) {
                        $item.closest('table').find('tbody tr').each(function (i, row) {
                            row = $(row);
                            if(newIndex < oldIndex) {
                                row.children().eq(newIndex).before(row.children()[oldIndex]);
                            } else if (newIndex > oldIndex) {
                                row.children().eq(newIndex).after(row.children()[oldIndex]);
                            }
                        });
                    }

                    _super($item, container)
                }
            });

            $('.sorted_table,.sorted_head tr').sortable("disable");

            $("#btn-save-table").on("click", function(e) {

                $(this).toggleClass("active")
                var method = $(this).hasClass("active") ? "enable" : "disable";

                $('.sorted_table td').toggleClass('disabled')
                $('.sorted_table th').toggleClass('disabled')
                $('.sorted_table td a,.sorted_table th a').toggleClass('disabled')
                $('.sorted_table,.sorted_head tr').sortable(method);

                $(this).text(function(i, text){
                    return text === "บันทึกหัวตาราง" ? "แก้ไขหัวตาราง" : "บันทึกหัวตาราง";
                })

                let array = [];
                let i =0;
                $('.sorted_body tr th').each(function() {
                    var data = $(this).data();
                    array.push({
                        key: i,
                        amulet_id: data.amulet || null,
                        job_id: data.job || null,
                    });
                    i++
                    console.log(array)
                });

                if(method === "disable"){
                    e.preventDefault();
                    let well = JSON.stringify(array);
                    console.log(well)
                    $('#update-table').val(well);
                    $('#form-update-table').submit();
                }

            });

            $('a.badge.badge-danger,span.oi-trash').click(function (e) {
                var data = $(this).data();
                $( "span#name" ).html( data.name );
                $( ".modal-footer a.btn-danger" ).attr( 'href', data.path+data.id );
            });

            $('a.btn-setting,span.oi-plus').click(function (e) {
                var data = $(this).data();
                $( "span#type" ).html( data.hr === 'amulet' ? 'เครื่องประดับ' : 'ชนิดของงาน' );
                $( "#form-add-table" ).attr( 'action', data.path );
            });

        });
    </script>
@stop

@endsection