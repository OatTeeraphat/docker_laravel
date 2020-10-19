@extends('layouts.app')

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

                <div class="nav-pills-container mb-3 not-print">
                    <ul class="nav nav-pills  justify-content-center justify-content-md-start">
                        <li class="nav-item mr-2">
                            <a class="nav-link" href="{{url('bill')}}">บิลรับงาน</a>
                        </li>
                        <li class="nav-item mr-2">
                            <a class="nav-link" href="{{url('recent')}}">ดูบิลเก่า</a>
                        </li>
                        <li class="nav-item mr-2">
                            <a class="nav-link active" href="{{url('summary')}}">สรุปรายวัน</a>
                        </li>
                    </ul>
                </div>

                <form method="POST" action="{{ url('summary') }}" autocomplete="off">
                        @csrf
                        <div class="card mb-2 mb-md-3 not-print">
                            <div class="card-body">
                                <div class="form-group row mb-0 justify-content-start">
                                    <div class="col-lg-4 col-md-12 col-12  mb-lg-0 form-group">
                                        <label for="inputdatepicker" class="report-label">สาขา</label>
                                        <select id="branch_id" class="custom-select custom-select-md" name="branch_id" >
                                            @if($role == 4)
                                                <option value="0" {{ isset($current) ? ( $current->branch == 0 ? 'selected' : '' ) : 'selected' }}>แสดงทุกสาขา</option>
                                                @foreach($branch as $b)
                                                    <option value="{{$b->id}}" {{ isset($current) ? ( $current->branch == $b->id ? 'selected' : '' ) : '' }} >{{$b->name}}</option>
                                                @endforeach
                                            @else
                                                @foreach($branch as $b)
                                                    <option value="{{$b->id}}" selected>{{$b->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12  mb-lg-0 form-group">
                                        <label for="inputdatepicker" class="report-label">ตั้งแต่วันที่</label>
                                        <div class="input-group">
                                            <input id="inputdatepicker" name="date_start"  class="datepicker datepicker1 form-control not-require"  value="{{isset($current) ? $current->start : ''}}">
                                            <div class="input-group-append input-append-button">
                                                <span class="input-group-text"><span class="oi oi-calendar"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12 mb-4  mb-lg-0 form-group">
                                        <label for="inputdatepicker2" class="report-label">จนถึงวันที่</label>
                                        <div class="input-group">
                                            <input id="inputdatepicker2" name="date_end"  class="datepicker datepicker2 form-control not-require"  value="{{isset($current) ? $current->end : ''}}">
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

                    <div class="card mb-3 mb-md-4" id="print-summary">

                        <div class="card-header not-print">
                            <h5>สรุปรายวัน<br class="d-flex d-md-none"><small class="text-muted float-md-right mt-1">{{$current->report_desc}}</small></h5>
                        </div>

                        <div class="row print-only">
                            <div class="col-12 px-4 my-0">
                                <div class="dt-print-header d-flex align-item-center summary-header">
                                    <img src="{{ url('/') }}/public/images/jewerly-t.png" />
                                    <div class="dt-haed-sub align-self-center">
                                        <h3>รายงานสรุปรายวัน</h3>
                                        <p>สาขา {{$current->report_desc}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--ค่าบริการ--}}
                        <div class="row justify-content-center print-table-row mt-4">
                            <div class="col-md-10 col-lg-7">
                                <div class="form-group row print-row">
                                    <label class="col-12 mb-3"><strong>ยอดค่าบริการ​ (บาท)</strong></label>
                                    <div class="col-12 col-md-12 col-p-8">
                                        <div class="table-responsive-sm">
                                            <table class="table table-bordered table-slim">
                                            <tbody>
                                            <tr>
                                                <td>ค่าซ่อม</td>
                                                <td class="text-right">
                                                    <input type="text" class="money-format text-right summary-form" readonly
                                                    value="{{ $data->order }}" >
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>ค่าส่วนประกอบ</td>
                                                <td class="text-right">
                                                    <input type="text" class="money-format text-right summary-form" readonly
                                                    value="{{ $data->material }}" >
                                                </td>
                                            </tr>
                                            <tr >
                                                <td><strong>รวมค่าบริการ</strong></td>
                                                <td class="text-right">
                                                    <input type="text" class="money-format text-right summary-form font-weight-bold" readonly
                                                           value="{{ $data->sumService }}" >
                                                </td>
                                            </tr>

                                            </tbody>
                                        </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--ชำระแล้ว--}}{{--ค้างชำระ--}}
                        <div class="row justify-content-center print-table-row">
                            <div class="col-md-10 col-lg-7">
                                <div class="form-group row print-row">
                                    <label class="col-12 mb-3"><strong>ยอดชำระค่าบริการ (บาท)</strong></label>
                                    <div class="col-12 col-md-12 col-p-8">
                                        <div class="table-responsive-sm">
                                            <table class="table table-bordered table-slim mb-2">
                                            <tbody>
                                            <tr>
                                                <td>เงินสด</td>
                                                <td class="text-right">
                                                    <input type="text" class="money-format text-right summary-form" readonly
                                                           value="{{ $data->cash }}" >
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>บัตรเครดิต</td>
                                                <td class="text-right">
                                                    <input type="text" class="money-format text-right summary-form" readonly
                                                           value="{{ $data->credit }}" >
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Voucher</td>
                                                <td class="text-right">
                                                    <input type="text" class="money-format text-right summary-form" readonly
                                                           value="{{ $data->voucher }}" >
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>บัตรฟรี หรือ คูปองอื่นๆ</td>
                                                <td class="text-right">
                                                    <input type="text" class="money-format text-right summary-form" readonly
                                                           value="{{ $data->coupon }} " >
                                                </td>
                                            </tr>
                                            <tr >
                                                <td><strong>รวมชำระแล้ว</strong></td>
                                                <td class="text-right">
                                                    <input type="text" class="money-format text-right summary-form font-weight-bold" readonly
                                                           value="{{ $data->sumPayService }} " >
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                            <table class="table table-bordered table-slim">
                                            <tbody>
                                            <tr >
                                                <td><strong>ยอดค้างชำระ</strong></td>
                                                <td class="text-right">
                                                    <input type="text" class="money-format text-right summary-form font-weight-bold" readonly
                                                           value="{{ $data->payment_remain }} " >
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-direction-column ">
                            {{--ใช้ทอง--}}
                            <div class="row justify-content-center print-table-row print-50">
                                <div class="col-md-10 col-lg-7">
                                    <div class="form-group row print-row mb-0">
                                        <label class="col-12 mb-3"><strong>ยอดใช้ทอง (กรัม)</strong></label>
                                        <div class="col-12 col-md-12 col-p-8">
                                            <div class="table-responsive-sm">
                                                <table class="table table-bordered table-slim">
                                                <tbody>
                                                @if(isset($data->craft))
                                                    @foreach($data->craft as $d)
                                                        <tr>
                                                            <td>{{$d->name}}</td>
                                                            <td class="text-right">
                                                                <input type="text" class="money-format text-right summary-form font-weight-bold" readonly
                                                                       value="{{$d->gold}} " >
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    <tr >
                                                        <td><strong>รวมใช้ทอง</strong></td>
                                                        <td class="text-right">
                                                            <input type="text" class="money-format text-right summary-form font-weight-bold" readonly
                                                                   value="{{$data->craft_sum}} " >
                                                        </td>
                                                    </tr>
                                                @endif

                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{--เงินหน้าร้าน--}}
                            <div class="row justify-content-center print-table-row print-50">
                                <div class="col-md-10 col-lg-7">
                                    <div class="form-group row print-row mb-0">
                                        <label class="col-12 mb-3"><strong>ยอดรับเงินหน้าร้าน (บาท)</strong></label>
                                        <div class="col-12 col-md-12 col-p-8">
                                            <div class="table-responsive-sm">
                                                <table class="table table-bordered table-slim">
                                                <tbody>
                                                <tr>
                                                    <td class="td-50">เงินสด</td>
                                                    <td class="text-right">
                                                        <input type="text" class="money-format text-right summary-form" readonly
                                                               value="{{$data->credit_cash}} " >
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="td-50">บัตรเครดิต</td>
                                                    <td class="text-right">
                                                        <input type="text" class="money-format text-right summary-form" readonly
                                                               value="{{$data->credit_credit}} " >
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td class="td-50">Voucher</td>
                                                    <td class="text-right">
                                                        <input type="text" class="money-format text-right summary-form" readonly
                                                               value="{{$data->credit_voucher}} " >
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="td-50">บัตรฟรี หรือ คูปองอื่นๆ</td>
                                                    <td class="text-right">
                                                        <input type="text" class="money-format text-right summary-form" readonly
                                                               value="{{$data->credit_coupon}} " >
                                                    </td>
                                                </tr>
                                                <tr >
                                                    <td class="td-50"><strong>รวมรับ</strong></td>
                                                    <td class="text-right">
                                                        <input type="text" class="money-format text-right summary-form font-weight-bold" readonly
                                                               value="{{$data->sumCredit}} " >
                                                    </td>
                                                </tr>

                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row justify-content-end print-only px-4">
                            ผู้ออกเอกสาร {{ Auth::user()->name }} {{$data->getDateServer}}
                        </div>

                    </div>

                    <div class="card mb-2 mb-md-3 not-print">
                        <div class="card-body text-center">
                            <button type="button" class="btn btn-primary btn-lg mr-2 col-12 col-md-4 col-lg-2 mb-0" id="btn-print" data-href="{{url('/recent/bill?id=')}}{{ isset($bill) ? $bill->bill_id : '' }}" >พิมพ์หน้านี้</button>
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



            $(".money-format").on("change", function() {
                let value = $(this).val();
                if ($.isNumeric(value) && value >= 0) {
                    $(this).val(formatMoney(value));
                    $(this).parent().addClass('not-null');
                }
                else {
                    $(this).val("");
                    $(this).parent().removeClass('not-null');
                }
            })
            
            $('#btn-print').click(function () {
                window.print();
            })

            function formatMoney(value){
                //console.log(value)
                return parseFloat(value, 10)
                    .toFixed(2)
                    .replace(/(\d)(?=(\d{3})+\.)/g, "$1,")
                    .toString()
            }

        });
    </script>

@stop