@extends('layouts.app')

@section('content')
@php
$role = Auth::user()->roles[0]->level;

@endphp

<div class="container">

    <div class="row justify-content-center">

        <div class="col-md-12">

            <div class="nav-pills-container mb-3 not-print">
                <ul class="nav nav-pills  justify-content-center justify-content-md-start">
                    <li class="nav-item mr-2">
                        <a class="nav-link" href="{{url('bill')}}">บิลรับงาน</a>
                    </li>
                    <li class="nav-item mr-2">
                        <a class="nav-link active" href="{{url('recent')}}">ดูบิลเก่า</a>
                    </li>
                    <li class="nav-item mr-2">
                        <a class="nav-link" href="{{url('summary')}}">สรุปรายวัน</a>
                    </li>
                </ul>
            </div>

            <form method="POST" action="{{ url('recent') }}" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="card mb-2 mb-md-3 not-print">
                    <div class="card-body">
                        <div class="form-group row mb-0 justify-content-start">
                            <div class="col-lg-4 col-md-12 col-12  mb-lg-0 form-group">
                                <label for="inputdatepicker" class="report-label">สาขา</label>
                                <select id="branch_id" class="custom-select custom-select-md" name="branch_id" >
                                    @if($role == 4)
                                        <option value="0" {{ isset($current) ? ( $current->branch_request == 0 ? 'selected' : '' ) : 'selected' }}>แสดงทุกสาขา</option>
                                        @foreach($branch as $b)
                                            <option value="{{$b->id}}" {{ isset($current) ? ( $current->branch_request == $b->id ? 'selected' : '' ) : '' }} >{{$b->name}}</option>
                                        @endforeach
                                    @else
                                        @foreach($branch as $b)
                                            @if(isset($current) && $b->id == $current->branch_id)

                                                <option value="{{$b->id}}" selected>{{$b->name}}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-6 col-12  mb-lg-0 form-group">
                                <label for="inputdatepicker" class="report-label">ตั้งแต่วันที่</label>
                                <div class="input-group">
                                    <input id="inputdatepicker" name="date_start"  class="datepicker datepicker1 form-control not-require"  value="{{isset($current) ? $current->date_start : ''}}">
                                    <div class="input-group-append input-append-button">
                                        <span class="input-group-text"><span class="oi oi-calendar"></span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-12 mb-4  mb-lg-0 form-group">
                                <label for="inputdatepicker2" class="report-label">จนถึงวันที่</label>
                                <div class="input-group">
                                    <input id="inputdatepicker2" name="date_end"  class="datepicker datepicker2 form-control not-require"  value="{{isset($current) ? $current->date_end : ''}}">
                                    <div class="input-group-append input-append-button">
                                        <span class="input-group-text input-group-text2"><span class="oi oi-calendar"></span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-12 mb-0 mb-lg-0 ">
                                <label for="" class="report-label d-none d-lg-inline-block"></label>
                                <div class="input-group">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block btn-report-search">ค้นหา</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            @if(!empty($bills))
                @foreach($bills as $index => $bill)
            <div class="card mb-2 mb-md-3">
                <div class="card-header">
                    <h5><strong>บิลวันที {{$index}}</strong></h5>
                    </div>
                <div class="card-body pt-4">
                    <div class="row justify-content-center">
                        <div class="col-md-11">
                            <div class="row">
                                <div class="col-md-3 col-6 mb-2">
                                    <select class="custom-select" id="{{'table_select_' .str_replace("/","",$index)}}">
                                        <option value="ยังไม่ปิดบิล" selected>ยังไม่ปิดบิล</option>
                                        <option value="ปิดบิลแล้ว">ปิดบิลแล้ว</option>
                                        <option value="">ทั้งหมด</option>
                                        </select>
                                    </div>
                                <div class="col-md-9 col-6 mt-2 d-none d-md-block">
                                    <p class="float-right mb-1">จำนวน : <span id="{{'tableCount_' .str_replace("/","",$index)}}">5</span> รายการ</p>
                                    </div>
                                <div class="col-12 col-md-12 mb-3">
                                    <div class="table-report table-responsive">
                                        <table id="{{'table_'. str_replace("/","",$index)}}" class="table table-hover table-bordered table-bill" style="width:100%">
                                            <thead>
                                            <tr>
                                                <th scope="col" class="disabled">บิล</th>
                                                <th scope="col" class="disabled">เลขที่บิล</th>
                                                <th scope="col" class="disabled">รูป</th>
                                                <th scope="col" class="disabled">ประเภท</th>
                                                <th scope="col" class="disabled">ลูกค้า</th>
                                                <th scope="col" class="disabled">ยอดรวม</th>
                                                <th scope="col" class="disabled">ใช้ทอง</th>
                                                <th scope="col" class="disabled text-center">ส่งงาน</th>
                                                <th scope="col" class="disabled text-center">ชำระแล้ว</th>
                                                <th scope="col" class="disabled text-center">ปิดบิล</th>
                                                <th scope="col" class="d-none">สถานะ</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($bill as $i => $data)
                                                <tr>
                                                    <td>
                                                        <a href="{{$data->bill_id ? url("/") .'/bill/update?id='. $data->bill_id : ''}}" class="badge badge-primary badge-icon not-pointer">
                                                            <span class="oi oi-pencil"></span>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <p>
                                                            <a href="{{$data->bill_id ? url("/") .'/bill/update?id='. $data->bill_id : ''}}" class="text-black">
                                                                {{$data->bill_id}}
                                                            </a>
                                                        </p>
                                                    <td>
                                                        <div class="img-fit"
                                                             @if(is_null($data->img))
                                                                style="background: url('{{url("/") }}/public/img/noimg.png')">
                                                             @else
                                                                style="background: url('{{url("/") . '/public/images/job/' . $data->img }}')">
                                                             @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @switch($data->job_type)
                                                            @case(1)
                                                                งานซ่อม
                                                            @break
                                                            @case(2)
                                                                แกะสลัก
                                                            @break
                                                            @default
                                                                อื่นๆ
                                                        @endswitch
                                                    </td>
                                                    <td>{{$data->customer_type}}</td>
                                                    <td class="text-right">{{$data->sum_cost}}</td>
                                                    <td class="text-right">{{$data->gold}}</td>
                                                    <td class="text-center">
                                                        @switch($data->deliver)
                                                            @case(1)
                                                            <span class="oi oi-circle-check"></span>
                                                            @break
                                                            @default
                                                            <span class="oi oi-circle-x"></span>
                                                        @endswitch
                                                    </td>
                                                    <td class="text-center">
                                                        @switch($data->pay)
                                                            @case(1)
                                                            <span class="oi oi-circle-check"></span>
                                                            @break
                                                            @default
                                                            <span class="oi oi-circle-x"></span>
                                                        @endswitch
                                                    </td>
                                                    <td class="text-center">
                                                        @switch($data->status)
                                                            @case(1)
                                                                <span class="oi oi-circle-check"></span>
                                                            @break
                                                            @case(2)
                                                                <span class="badge badge-secondary">ยกเลิก</span>
                                                            @break
                                                            @default
                                                                <span class="oi oi-circle-x"></span>
                                                        @endswitch
                                                    </td>
                                                    <td class="d-none">
                                                        @if($data->status == 0)
                                                            ยังไม่ปิดบิล
                                                            @else
                                                            ปิดบิลแล้ว
                                                        @endif
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
            @endforeach
            {{--<div id="feedContainer"></div>--}}
            {{--<div id="ajax_load" class="row justify-content-center">--}}
                {{--<img src="{{url('/')}}/public/img/loading-lg.gif" alt="">--}}
            {{--</div>--}}
            {{--<div id="ajax_nomore" class="row justify-content-center mt-5 my-3" >--}}
                {{--<h3 class="text-muted">ไม่มีบิลที่จะแสดงแล้ว</h3>--}}
            {{--</div>--}}
            @else
                <div class="bill-not-found text-center mt-5 my-5 py-5">
                    <h3 class=" mb-0">ไม่พบรายการ</h3>
                    <small class="text-muted">ไม่มีการทำรายการในช่วงเวลานี้</small>
                </div>
            @endif

        </div>

    </div>

</div>

@endsection

@section('scripts')

<script>

    $(document).ready(function($) {

    @foreach($bills as $index => $bill)

        let tab{{str_replace("/","",$index)}} = $("{{'#table_'. str_replace("/","",$index) }}").DataTable({
            responsive: true,
            order: [[ 1, "asc" ]],
            columnDefs:[
                {
                    targets: [7,8,9],
                    width: "20px"
                },
                {
                    targets: [4],
                    width: "110px"
                },
                {
                    targets: [1],
                    width: "120px"
                },
                {
                    targets: [3],
                    width: "50px"
                },
                {
                    targets: [0],
                    orderable: false
                },
            ],
            paging : false,
            bInfo: false,
            language: {
                zeroRecords: "ไม่พบบิลที่ต้องการ จากทั้งหมด"
            },
            oSearch: {sSearch: "ยังไม่ปิดบิล"}
        });

        $("{{'#table_select_'. str_replace("/","",$index) }}").on('change',function () {
            let value = $(this).val();
            $("{{'input[aria-controls=table_'.str_replace("/","",$index) . ']'}}").val(value).trigger('keyup');
            $("{{'#tableCount_' .str_replace("/","",$index)}}").text(tab{{str_replace("/","",$index)}}.$('tr', {"filter":"applied"}).length)
        })

        $("{{'#table_select_'. str_replace("/","",$index) }}").trigger('change');

    @endforeach

    $(function () {
        $('#inputdatepicker').val() === ''
            ? $('#inputdatepicker').datepicker("setDate", "0")
            : $('#inputdatepicker').datepicker("setDate", "{{isset($current) ? $current->date_start : ''}}");
        $('#inputdatepicker2').val() === ''
            ? $('#inputdatepicker2').datepicker("setDate", "0")
            : $('#inputdatepicker2').datepicker("setDate", "{{isset($current) ? $current->date_end : ''}}");
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

    $('.input-group-text').click(function(){
        $('#inputdatepicker2').data("datepicker").hide();
        $('#inputdatepicker').data("datepicker").show();
    });

    $('#inputdatepicker').on('focus',function () {$(this).blur();})

    $('#inputdatepicker2').datepicker({
        autoclose: true,
        format: 'dd/mm/yyyy',
        todayBtn: 'linked',
        keyboardNavigation: false,
        language: 'th',
        thaiyear: true,
    }).prop("readonly", true);

    $('.input-group-text2').click(function(){
        $('#inputdatepicker').data("datepicker").hide();
        $('#inputdatepicker2').data("datepicker").show();
    });

    $('#inputdatepicker2').on('focus',function () {$(this).blur();})


    })

</script>
@stop