@extends('layouts.app')
{!! config(['app.title' => 'Manage Users']) !!}
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
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

                <div class="row">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent py-0">
                            <li class="breadcrumb-item active" aria-current="page">ตั้งค่าข้อมูลบิล</li>
                        </ol>
                    </nav>
                </div>

                <form method="POST" id="create_main" action="{{ url('setting/update') }}" autocomplete="off">
                    @csrf
                    <div class="card mb-3 mb-md-4">
                            <div class="card-header">
                                <h5>ตั้งค่าข้อมูลบิล <small class="text-muted float-right">หัวกระดาษ</small></h5>
                            </div>

                            <div class="card-body">

                                <div class="row justify-content-center">
                                    <div class="col-md-10 col-lg-8">
                                        <div class="form-group row required">
                                            <label for="head_r_1" class="col-12">หัวกระดาษด้านขวา 1</label>
                                            <div class="col-12">
                                                <input id="head_r_1" type="text" class="form-control" name="head_r_1" autofocus
                                                       value="{{ isset($setting) ? $setting->head_r_1 : ''}}" onkeyup="countInput('head_r_1',60)">
                                                <span class="invalid-feedback">
                                                    <span class="text-primary text-count"></span>
                                                    <span class="text-muted">แนะนำบริการของร้าน - 60 ตัวอักษร</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row justify-content-center">
                                    <div class="col-md-10 col-lg-8">
                                        <div class="form-group row required">
                                            <label for="head_top_r_2" class="col-12">หัวกระดาษด้านขวา 2</label>
                                            <div class="col-12">
                                                <input id="head_r_2" type="text" class="form-control" name="head_r_2" autofocus
                                                       value="{{ isset($setting) ? $setting->head_r_2 : ''}}" onkeyup="countInput('head_r_2',30)">
                                                <span class="invalid-feedback">
                                                    <span class="text-primary text-count"></span>
                                                    <strong><span class="text-muted">เว็บไซต์ของร้าน - 30 ตัวอักษร</span></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    <div class="card mb-2 mb-md-4">
                        <div class="card-header">
                            <h5>ตั้งค่ารายการงานซ่อม</h5>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <a class="btn btn-primary btn-lg" id="btn-save-table" href="javascript:void(0)" role="button">แก้ไขหัวตาราง</a>
                                    <hr>
                                </div>
                            </div>
                            @php
                                $count_a = count($amulet)+1;
                                $container = '100%';
                            @endphp
                            <div class="row">
                                <div class="col-12">
                                    <input type="password" class="d-none" />
                                    <div class="table-responsive">
                                        <div class="table-sort-container">
                                            <div class="table-sort-amulet" style="width: {{$container}};" >
                                                <table class="table table-sort table-bordered sorted_table" id="sorted_table_job">
                                                    <thead class="sorted_head" id="sorted_head_job">
                                                    <tr>
                                                        <th class="static disabled" width="{{ 100/$count_a }}%"></th>
                                                        @foreach ($amulet as $i => $a)
                                                        <th class="disabled" width="{{ 100/$count_a }}%" data-amulet="{{$a->id}}" id="amulet-{{$a->id}}">
                                                            <a href=""
                                                               data-id='{{$a->id}}'
                                                               data-name="{{$a->name}}"
                                                               data-type="amulet"
                                                               data-path='{{ url('api/setting/updateAmulet?id=') }}'
                                                               class="badge badge-primary mr-5 badge-icon"
                                                               data-toggle="modal"
                                                               data-target="#deleteModal" id="{{ $i+1 }}"
                                                            >
                                                                <span class="oi oi-pencil" id="{{ $i+1 }}"></span>
                                                            </a>
                                                            {{$a->name}}
                                                        </th>
                                                        @endforeach
                                                    </tr>
                                                    </thead>
                                                    <tbody class="sorted_body" id="sorted_body_job">
                                                    @foreach ($job as $i => $j)
                                                        <tr>
                                                            <th class="disabled" data-job="{{$j->id}}" id="job-{{$j->id}}" >
                                                                <a href=""
                                                                   data-id='{{$j->id}}'
                                                                   data-name='{{$j->name}}'
                                                                   data-type="job"
                                                                   data-path='{{ url('api/setting/updateJob?id=') }}'
                                                                   class="badge badge-primary mr-5 badge-icon"
                                                                   data-toggle="modal"
                                                                   data-target="#deleteModal" id="{{ $i+1 }}"
                                                                >
                                                                    <span class="oi oi-pencil" id="{{ $i+1 }}"></span>
                                                                </a>
                                                                {{$j->name}}
                                                            </th>
                                                            @foreach ($amulet as $q => $a)
                                                                <td>
                                                                </td>
                                                            @endforeach
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

                    <div class="card mb-2 mb-md-4">
                            <div class="card-header">
                                <h5>ตั้งค่ารายการส่วนประกอบ</h5>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <a class="btn btn-primary mr-2 btn-lg" id="btn-save-table-material" href="javascript:void(0)" role="button">แก้ไขหัวตาราง</a>
                                        <hr>
                                    </div>
                                </div>

                                <div class="row justify-content-center">
                                    <div class="col-12">
                                        <input type="password" class="d-none" />
                                        <div class="table-responsive">
                                            <div class="table-sort-container">
                                                <div class="table-sort-amulet" >
                                                    <table class="table table-sort table-bordered sorted_table" id="sorted_table_material">
                                                        <thead class="sorted_head" id="sorted_head_material">
                                                        <tr>
                                                            <th class="disabled" width="110px">รายการ</th>
                                                            <th class="disabled min-width-inject" width="120px">ราคา</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody class="sorted_body" id="sorted_body_material">
                                                        @foreach ($material as $i => $m)
                                                            <tr>
                                                                <th class="disabled" data-job="{{$m->id}}" width="110px">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                        <a href=""
                                                                           data-id='{{$m->id}}'
                                                                           data-name='{{$m->name}}'
                                                                           data-type = 'material'
                                                                           data-path='{{ url('material/update?id=') }}'
                                                                           class="badge badge-primary badge-icon mr-1 float-left"
                                                                           data-toggle="modal"
                                                                           data-target="#deleteModal" id="{{ $i+1 }}"
                                                                        >
                                                                            <span class="oi oi-pencil" id="{{ $i+1 }}"></span>
                                                                        </a>
                                                                        <a href=""
                                                                           data-id='{{$m->id}}'
                                                                           data-name='{{$m->name}}'
                                                                           data-path='{{ url('material/delete?id=') }}'
                                                                           class="badge badge-danger badge-icon mr-4 float-left"
                                                                           data-toggle="modal"
                                                                           data-target="#deleteMaterialModal" id="{{ $i+1 }}"
                                                                        >
                                                                            <span class="oi oi-trash" id="{{ $i+1 }}"></span>
                                                                        </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-12">{{$m->name}}</div>
                                                                    </div>

                                                                </th>
                                                                <td></td>
                                                            </tr>
                                                        @endforeach
                                                        <tr class="static">
                                                            <td style="height: 45px; padding: 10px;">
                                                                <a href=""
                                                                   class="badge btn-primary btn btn-setting-btm btn-setting badge-icon mr-1"
                                                                   aria-disabled="true"
                                                                   role="button"
                                                                   data-hr="job"
                                                                   data-path="{{ url('material/addMaterial') }}"
                                                                   data-toggle="modal"
                                                                   data-target="#addMaterialModal"
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

                    <div class="card mb-2 mb-md-4">
                            <div class="card-header">
                                <h5>ตั้งค่าข้อมูลบิล <small class="text-muted float-right">ท้ายกระดาษ</small></h5>
                            </div>

                            <div class="card-body">

                                <div class="row justify-content-center">
                                    <div class="col-md-10 col-lg-8">
                                        <div class="form-group row required">
                                            <label for="btm_l_1" class="col-12">ท้ายกระดาษด้านซ้าย 1</label>
                                            <div class="col-12">
                                                <textarea id="btm_l_1" type="text" class="form-control" name="btm_l_1" autofocus
                                                          onkeyup="countInput('btm_l_1',120)" rows="2" >{{ isset($setting) ? $setting->btm_l_1 : ''}}</textarea>
                                                <span class="invalid-feedback">
                                                    <span class="text-primary text-count"></span>
                                                    <span class="text-muted">ข้อตกลงหากเกิดความเสียหาย - 120 ตัวอักษร</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row justify-content-center">
                                    <div class="col-md-10 col-lg-8">
                                        <div class="form-group row required">
                                            <label for="btm_l_2" class="col-12">ท้ายกระดาษด้านซ้าย 2</label>
                                            <div class="col-12">
                                                <textarea id="btm_l_2" type="text" class="form-control" name="btm_l_2" autofocus
                                                          onkeyup="countInput('btm_l_2',120)" rows="2">{{ isset($setting) ? $setting->btm_l_2 : ''}}</textarea>
                                                <span class="invalid-feedback">
                                                    <span class="text-primary text-count"></span>
                                                    <span class="text-muted">ข้อตกลงหากเกิดความเสียหาย(อังกฤษ) - 120 ตัวอักษร</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row justify-content-center">
                                    <div class="col-md-10 col-lg-8">
                                        <div class="form-group row required">
                                            <label for="btm_r_1" class="col-12">ท้ายกระดาษด้านขวา 1</label>
                                            <div class="col-12">
                                                <input id="btm_r_1" type="text" class="form-control" name="btm_r_1" autofocus
                                                       value="{{ isset($setting) ? $setting->btm_r_1 : ''}}" onkeyup="countInput('btm_r_1',40)">
                                                <span class="invalid-feedback">
                                                    <span class="text-primary text-count"></span>
                                                    <span class="text-muted">ยืนยันรับสินค้าแล้ว - 40 ตัวอักษร</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row justify-content-center">
                                    <div class="col-md-10 col-lg-8">
                                        <div class="form-group row required">
                                            <label for="btm_r_2" class="col-12">ท้ายกระดาษด้านขวา 2</label>
                                            <div class="col-12">
                                                <input id="btm_r_2" type="text" class="form-control" name="btm_r_2" autofocus
                                                       value="{{ isset($setting) ? $setting->btm_r_2 : ''}}" onkeyup="countInput('btm_r_2',40)">
                                                <span class="invalid-feedback">
                                                    <span class="text-primary text-count"></span>
                                                    <span class="text-muted">ยืนยันรับสินค้าแล้ว(อังกฤษ) - 40 ตัวอักษร</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    <div class="card mt-3 mb-1">
                        <div class="card-body" >
                            <div class="col-12">
                                <div class="row justify-content-center" id="btm-button">
                                    <button type="submit" class="btn btn-primary btn-lg mr-2 col-12 col-md-4 col-lg-2 mb-2 mb-md-0" >บันทึกแก้ไข</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <form id="form-update-table" action=""  method="POST">
        @csrf
        <input name="data" type="hidden" id="update-table" class="d-none">
    </form>


    <!-- Modal-mantanent -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="d-none">
            <input type="password"/>
        </div>
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-body py-4">
                    <div class="">
                        <h3 class="text-center mb-0">แก้ไข <span id="type"></span></h3>
                        <p class="text-center"><span id="name"></span> จะถูกเปลี่ยนแปลงในตาราง</p>
                    </div>

                    <form id="form-add-table" method="POST" autocomplete="off">
                        @csrf
                        <div class="form-group row required justify-content-center">
                            <label for="name" class="col-md-10">ชื่อ</label>
                            <div class="col-md-10">
                                <input id="name" type="text" class="form-control" name="name" required autofocus>
                                <input id="id" type="text" class="d-none" name="id">
                            </div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary btn-lg" id="submit-add"
                            onclick="event.preventDefault();
                        document.getElementById('form-add-table').submit();"
                    >แก้ไขตัวเลือก</button>
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">ยกเลิก</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addMaterialModal" tabindex="-1" role="dialog" aria-labelledby="addMaterialModal" aria-hidden="true">
        <div class="d-none">
            <input type="password"/>
        </div>
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-body py-4">
                    <div class="">
                        <h3 class="text-center mb-0">เพิ่มรายการส่วนประกอบ</h3>
                        <p class="text-center">รายการจะเพิ่มใหม่ ในรายการส่วนประกอบ</p>
                    </div>

                    <form id="form-add-material" method="POST" autocomplete="off">
                        @csrf
                        <div class="form-group row required justify-content-center">
                            <label for="name" class="col-md-10">ชื่อ</label>
                            <div class="col-md-10">
                                <input id="name_M" type="text" class="form-control" name="name" required autofocus>
                            </div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary btn-lg" id="submit-add"
                            onclick="event.preventDefault();
                        document.getElementById('form-add-material').submit();"
                    >เพิ่มตัวเลือก</button>
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">ยกเลิก</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteMaterialModal" tabindex="-1" role="dialog" aria-labelledby="deleteMaterialModal" aria-hidden="true">
        <div class="d-none">
            <input type="password"/>
        </div>
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-body py-4">
                    <div class="">
                        <h3 class="text-center mb-0">ลบรายการ <span id="name"></span></h3>
                        <p class="text-center">รายการนี้จะไม่ถูกแสดง</p>
                    </div>

                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">ยกเลิก</button>
                    <a role="button" class="btn btn-danger btn-lg" id="submit-add">ยืนยันลบ</a>
                </div>
            </div>
        </div>
    </div>


@section('scripts')
    <style>
        .placeholder{ width: {{ 100/$count_a }}%; }
    </style>
    <script>
        $(document).ready(function($) {
            $('input[type="text"],textarea').trigger('keyup');

            var oldIndex;
            $('#sorted_table_job').sortable({
                containerSelector: 'table',
                itemPath: '> tbody#sorted_body_job',
                itemSelector: 'tr:not(.static)',
                helper: "clone",
                placeholder: '<tr class="placeholder"/>',
                onDragStart: function($item, container, _super) {
                    oldIndex = $item.index();
                    $item.children().width(300)
                    _super($item, container);
                },
            });

            $('#sorted_head_job tr').sortable({
                containerSelector: 'tr',
                itemSelector: 'th:not(.static)',
                placeholder: '<th class="placeholder"/>',
                helper: "clone",
                vertical: false,
                onDragStart: function ($item, container, _super) {
                    oldIndex = $item.index();
                    $item.appendTo($item.parent());
                    _super($item, container);
                },
                onDrop: function  ($item, container, _super) {
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

                    _super($item, container);
                }
            });

            $('#sorted_table_job,#sorted_head_job tr,#sorted_table_material').sortable("disable");

            $("#btn-save-table").on("click", function(e) {

                $(this).toggleClass("active")
                var method = $(this).hasClass("active") ? "enable" : "disable";

                $('#sorted_table_job td').toggleClass('disabled')
                $('#sorted_table_job th').toggleClass('disabled')
                $('#sorted_table_job td a,.sorted_table th a').toggleClass('disabled')
                $('#sorted_table_job,#sorted_head_job tr').sortable(method);

                $(this).text(function(i, text){
                    return text === "บันทึกหัวตาราง" ? "แก้ไขหัวตาราง" : "บันทึกหัวตาราง";
                })

                let array = [];
                let i =0;
                $('#sorted_head_job tr th,#sorted_table_job tr th').each(function() {
                    var data = $(this).data();
                    var cell = $(this).html();
                    array.push({
                        key: i,
                        item: cell,
                        amulet_id: data.amulet || null,
                        job_id: data.job || null,
                    });
                    i++
                    console.log(cell)
                });

                if(method === "disable"){
                    e.preventDefault();
                    let well = JSON.stringify(array);
                    //console.log(well)
                    $('#update-table').val(well);
                    $( "#form-update-table" ).attr( 'action', "{{ url('api/setting/order')}}").submit();
                }

            });

            var oldIndex1;
            $('#sorted_table_material').sortable({
                containerSelector: 'table',
                itemPath: '> tbody#sorted_body_material',
                itemSelector: 'tr:not(.static)',
                helper: "clone",
                placeholder: '<tr class="placeholder" width="120px"/>',
                onDragStart: function($item, container, _super) {
                    oldIndex1 = $item.index();
                    $item.children().width(90)
                    _super($item, container);
                },
            });

            $("#btn-save-table-material").on("click", function(e) {

                $(this).toggleClass("active")
                var method = $(this).hasClass("active") ? "enable" : "disable";

                $('#sorted_table_material td').toggleClass('disabled')
                $('#sorted_table_material th').toggleClass('disabled')
                $('#sorted_table_material td a,#sorted_table_material th a').toggleClass('disabled')
                $('#sorted_table_material,#sorted_head_material tr').sortable(method);

                $(this).text(function(i, text){
                    return text === "บันทึกหัวตาราง" ? "แก้ไขหัวตาราง" : "บันทึกหัวตาราง";
                })

                let array = [];
                let i =0;
                $('#sorted_table_material tr th').each(function() {
                    var data = $(this).data();
                    var cell = $(this).html();
                    array.push({
                        key: i,
                        item: cell,
                        amulet_id: data.amulet || null,
                        job_id: data.job || null,
                    });
                    i++
                    //console.log(cell)
                });

                if(method === "disable"){
                    e.preventDefault();
                    let well = JSON.stringify(array);
                    //console.log(well)
                    $('#update-table').val(well);
                    $( "#form-update-table" ).attr( 'action', "{{ url('material/order') }}").submit();

                }

            });

            $('a.badge.badge-primary,span.oi-pencil').click(function (e) {
                var data = $(this).data();
                $("input[name='name']").val(data.name);
                $("input[name='id']").val(data.id);
                $( "span#name" ).html( data.name );
                $( "span#type" ).html( data.type === 'amulet' ? 'เครื่องประดับ'
                                                        : 'material' ? 'ส่วนประกอบ'
                                                            : 'job' ? 'ชนิดของงาน'
                                                                : null );
                $( "#form-add-table" ).attr( 'action', data.path+data.id);
            });

            $('a.badge.badge-danger,span.oi-trash').click(function (e) {
                var data = $(this).data();
                $( "span#name" ).html( data.name );
                $( ".modal-footer a.btn-danger" ).attr( 'href', data.path+data.id );
            });

            $('a.btn-setting,span.oi-plus').click(function (e) {
                var data = $(this).data();
                $( "#form-add-material" ).attr( 'action', data.path );
            });



        });

        function countInput(selector,max) {
            let s = '#'+ selector;
            let lengthTxt = $(s).val().length;
            let lengthRemain = max - lengthTxt

            let elm = $(s).parent()
                        .children('.invalid-feedback')
                        .children('.text-count')
                        .prepend('b')

            elm.text( lengthRemain >= 0 ? 'เหลือ '+lengthRemain+' '  : 'เกินจำนวน' )

            if (lengthRemain < 0){
                $(s).addClass('border border-danger');
                elm.removeClass('text-dark').addClass('text-danger')
            } else {
                $(s).removeClass('border border-danger');
                elm.addClass('text-dark').removeClass('text-danger')
            }
        }


    </script>
@stop

@endsection