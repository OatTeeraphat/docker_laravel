@extends('layouts.app')

@section('content')

    @php
        //var_dump($data);
        $role = Auth::user()->roles[0]->level;
    @endphp

    <div class="container">
        <div class="row justify-content-center mb-0">
            <div class="col-md-12">
                <div class="row">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent py-0">
                            <li class="breadcrumb-item active" aria-current="page">จัดการฐานข้อมูล</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card mb-3 mb-md-4">
                    <div class="card-header">
                        <h5>สำรองบิลรับงาน</h5>
                    </div>

                    <div class="card-body">

                        <div class="row justify-content-center">
                            <div class="col-12 col-md-10">
                                <form method="POST" id="update_deliver" action="{{ url('/export/dump_by_length') }}"  autocomplete="off">
                                        @csrf
                                    <div class="form-group row required">
                                        <div class="col-lg-5 col-md-6 col-12  mb-lg-0 form-group">
                                            <label for="inputdatepicker" class="report-label">ตั้งแต่วันที่</label>
                                            <div class="input-group">
                                                <input id="inputdatepicker" name="date_start"  class="datepicker datepicker1 form-control not-require"  value="{{isset($current) ? $current->start : ''}}">
                                                <div class="input-group-append input-append-button">
                                                    <span class="input-group-text"><span class="oi oi-calendar"></span></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-5 col-md-6 col-12 mb-4  mb-lg-0 form-group">
                                            <label for="inputdatepicker2" class="report-label">จนถึงวันที่</label>
                                            <div class="input-group">
                                                <input id="inputdatepicker2" name="date_end"  class="datepicker datepicker2 form-control not-require"  value="{{isset($current) ? $current->end : ''}}">
                                                <div class="input-group-append input-append-button">
                                                    <span class="input-group-text input-group-text2"><span class="oi oi-calendar"></span></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-6 col-12 mb-0 mb-lg-0 ">
                                            <div class="input-group mt-md-4 pt-1">
                                                <button type="button" onclick="save()" class="btn btn-primary btn-lg btn-block btn-report-search">ค้นหา</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card mb-3 mb-md-4">
                    <div class="card-header">
                        <h5>รายการสำรองฐานข้อมูล</h5>
                    </div>

                    <div class="card-body">

                        <div class="row justify-content-center">
                            <div class="col-12 col-md-10">
                                <div class="form-group row required">
                                    <label for="head_r_1" class="col-12">ฐานข้อมูล(ราย 7วัน)</label>
                                    <div class="col-12">
                                        <table class="table table-striped table-bordered table-report">

                                            <thead>
                                                <tr>
                                                    <th width="135px">วันที่</th>
                                                    <th>ไฟลล์</th>
                                                    <th width="135px">จำนวนออร์เดอร์</th>
                                                    <th width="80px">ดาวน์โหลด</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($backup_week as $key => $week)
                                                <tr>
                                                    <td>{{date('d-m-Y', strtotime($week->updated_at))}}</td>
                                                    <td>{{$week->file_name}}</td>
                                                    <td>{{$week->no_record}}</td>
                                                    <td><a href="{{ '/export/dump/' . $week->file_name}}" class="badge badge-primary badge-icon" download><span class="oi oi-data-transfer-download"></span></a></td>
                                                </tr>
                                                @endforeach
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-12 col-md-10">
                                <div class="form-group row required">
                                    <label for="head_r_1" class="col-12">ฐานข้อมูล(รายเดือน)</label>
                                    <div class="col-12">
                                        <table class="table table-striped table-bordered table-report">

                                            <thead>
                                            <tr>
                                                <th width="135px">วันที่</th>
                                                <th>ไฟลล์</th>
                                                <th width="135px">จำนวนออร์เดอร์</th>
                                                <th width="80px">ดาวน์โหลด</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($backup_month as $key => $month)
                                                <tr>
                                                    <td>{{date('d-m-Y', strtotime($month->updated_at))}}</td>
                                                    <td>{{$month->file_name}}</td>
                                                    <td>{{$month->no_record}}</td>
                                                    <td><a href="{{ '/export/dump/' . $month->file_name}}" class="badge badge-primary badge-icon" download><span class="oi oi-data-transfer-download"></span></a></td>
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
        </div>

    </div>


    <div class="modal fade" id="myModal2" tabindex="0" role="dialog" aria-labelledby="myModal2" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body pt-4">
                    <h2 class="text-center">กำลังสำรองไฟลล์</h2>
                    <p class="text-center" id="txt_status"></p>
                </div>
                <div class="modal-footer justify-content-center">
                    <div class="progress" id="progress" style="height: 20px; width: 100%">
                        <div id="upload-bar" data-sucess="0" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuemin="0" aria-valuemax="100"  style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal3" tabindex="0" role="dialog" aria-labelledby="myModal3" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body pt-4">
                    <h2 class="text-center">ต้องการลบข้อมูลบิล</h2>
                    <p class="text-center" id="text_length"></p>
                    <div class="row justify-content-center">
                        <div class="custom-control custom-checkbox mr-sm-2">
                            <input type="checkbox" class="custom-control-input" id="customControlAutosizing">
                            <label class="custom-control-label" for="customControlAutosizing">สำรองข้อมูลเรียบร้อยแล้ว</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <div class="row justify-content-center">
                        <div class="btn btn-danger btn-lg btn-block col-5 w-100 mr-3 disabled" style="width: 130px !important; height: 45px;" id="btn-conferm" disabled>ยืนยันลบ</div>
                        <div class="btn btn-secondary btn-lg btn-block col-5 w-100 mt-0" style="width: 130px !important;" data-dismiss="modal">ยกเลิก</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button type="button" class="d-none" id="myModal2-btn" data-toggle="modal" data-target="#myModal2">
    </button>

    <button type="button" class="d-none" id="myModal3-btn" data-toggle="modal" data-target="#myModal3">
    </button>



@endsection


@section('scripts')

    <script>
        $(document).ready(function($) {

            $(function () {
                $('#inputdatepicker').val() === ''
                    ? $('#inputdatepicker').datepicker("setDate", "0")
                    : $('#inputdatepicker').datepicker("setDate", "{{isset($current) ? $current->start : ''}}");
                $('#inputdatepicker2').val() === ''
                    ? $('#inputdatepicker2').datepicker("setDate", "0")
                    : $('#inputdatepicker2').datepicker("setDate", "{{isset($current) ? $current->end : ''}}");
                $('.money-format').trigger('change');
            });

            $('#inputdatepicker').datepicker({
                autoclose: true,
                format: 'dd/mm/yyyy',
                todayBtn: 'linked',
                keyboardNavigation: false,
                language: 'th',
                thaiyear: true,
            }).prop("readonly", true);

            $('.input-group-text').click(function () {
                $('#inputdatepicker2').data("datepicker").hide();
                $('#inputdatepicker').data("datepicker").show();
            });

            $('#inputdatepicker').on('focus', function () {
                $(this).blur();
            })

            $('#inputdatepicker2').datepicker({
                autoclose: true,
                format: 'dd/mm/yyyy',
                todayBtn: 'linked',
                keyboardNavigation: false,
                language: 'th',
                thaiyear: true,
            }).prop("readonly", true);

            $('.input-group-text2').click(function () {
                $('#inputdatepicker').data("datepicker").hide();
                $('#customControlAutosizing').trigger('change')
                $('#inputdatepicker2').data("datepicker").show();
            });

            $("#myModal3-btn").on('click',function () {
                $('#customControlAutosizing').get(0).checked = false;
                $('#btn-conferm').addClass('disabled')
                $('#text_length').text('ลบบิลช่วงวันที่ '+ $("#inputdatepicker").val() + ' ถึง ' + $("#inputdatepicker2").val())
            })

            $('#customControlAutosizing').on('change',function () {
                if ($(this).get(0).checked){
                    $('#btn-conferm').prop('disabled' ,true).removeClass('disabled')
                } else {
                    $('#btn-conferm').prop('disabled',false).addClass('disabled')
                }

            })

            $('#btn-conferm').on('click', function(e){
                if($('#btn-conferm').prop('disabled')){
                    drop();
                    e.preventDefault();
                }
            })

        })



    </script>


    <script>

        async function drop() {

            $("#myModal3-btn").trigger("click");

            const sleep = m => new Promise(r => setTimeout(r, m))
            await sleep(500)

            let date_length = $("#update_deliver").serialize();
            const remove_sucess = await _post(date_length, "{{ url('api/export/dropLength') }}").then((res) => res);

            if(await remove_sucess){
                window.location.href = "{{ url('')}}";
            }

        }

        async function save() {

            $("#upload-bar").width("0%");

            $("#myModal2-btn").trigger("click");

            $("#txt_status").text("กรุณาอย่าปิดหน้าต่างนี้ จนกว่าขั้นตอนจะสำเร็จ..");

            let date_length = $("#update_deliver").serialize()
            const chunk = await _post(date_length, "{{ url('api/export/getLength') }}").then((res) => res);

            $("#upload-bar").width("7%");


            const sleep = m => new Promise(r => setTimeout(r, m))
            await sleep(2000)
            $("#txt_status").text("กำลังเริ่มต้นสำรองข้อมูล ..");

            $("#upload-bar").width("25%");

            const _att = [];
            let data = await Promise.all(
                await $.map( chunk.chunkList , async function(item){
                    return _post(item, "{{ url('api/export') }}").then((res) => {

                        _att.push(1);
                        let _width = 50;
                        let chunk_lenght = chunk._length;
                        let perset = (_width/chunk_lenght) * _att.length;

                        $("#txt_status").text("กำลังดาวโหลดข้อมูล..[" + _att.length + "/" + chunk_lenght + "]");

                        $("#upload-bar").width( toString(perset + 15 ) + "%" );

                        console.log( 'file successful', perset, _att.length );
                        return res
                    });
                }))

            let start_date = $('#inputdatepicker').val().replace("-", "");
            let end_date = $('#inputdatepicker2').val().replace("-", "");

            let file_name = "backup_jwc_" + start_date + "_" + end_date + ".xlsx";
            let option = { "fileName": file_name };

            $("#txt_status").text("กรุณารอสักครู่ กำลังทำไฟลล์..");
            $("#upload-bar").width("70%");
            await sleep(2000 * _att.length)
            $("#upload-bar").width("75%");
            await sleep(1500)
            $("#upload-bar").width("90%");
            await sleep(1500 * _att.length)
            $("#upload-bar").width("100%");
            $("#myModal2-btn").trigger("click");


            Jhxlsx.export(data, option);

            $("#myModal3-btn").trigger("click");

        }

         async function _post (data, url) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let result = [];

            await $.ajax({
                type: "POST",
                url: url,
                data: data,
                timeout: 999999999,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data, textStatus, xhr)
                {
                    if (xhr.status === 200){

                        result = JSON.parse(data);

                    } else {

                        console.log('err');

                    }
                },
                statusCode: {
                    401: function() {
                        window.location.href = "{{ url('') }}"; //or what ever is your login URI
                    },
                    500: function() {
                        console.log('err')
                    }
                }
            });

            return result

        }



    </script>


@stop
