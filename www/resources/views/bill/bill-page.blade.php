@extends('layouts.app')

@section('content')

    @php
        if (isset($billData[0])) {
            $bill = $billData[0] ;
            $date = explode('/',$billData[0]->date);
            $dateBc = intval($date[0]) . '/' . intval($date[1]) . '/' . strval(intval($date[2]) - 543);
            $customer = $billData[0]->customer[0];
            $order = $billData[0]->order;
            $part = $billData[0]->part;
            $branch = $userData[0]->branch;
            //echo $branch;
            //echo $bill;
        }

        function jobTable($order, $j, $a)
        {

            if (isset($order)){
                foreach ($order as $data) {
                    if (($data->amulet_id == $a) and ($data->job_id == $j)){
                        return $data;
                    }
                };
            }
        }

        function materialTable($part, $m)
        {
            if (isset($part)){
            foreach ($part as $data) {
                if ( $data->material_id == $m){
                    return $data;
                }
            };
            }
        }

    @endphp

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-md-12 print-md-12">

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

                @if(Session::has('success'))
                    <div class="alert alert-success not-print" role="alert">
                        <span class="oi oi-check"></span> {{ Session::get('success') }}
                    </div>
                    <script>
                        $(".alert-success").fadeOut(3000, function(){
                            $(".alert-success").fadeOut(1000);
                        });
                    </script>
                @endif

                <div class="card card-print">

                    <div class="card-header not-print">
                        <h5>
                            <strong>
                                บิลเลขที่ {{isset($bill) ? $bill->bill_id : ''}}
                            </strong>
                        </h5>
                    </div>

                    <div class="card-body pt-4 card-print">
                        <div class="col-12">
                        <div class="row justify-content-center">
                            <div class="print-responsive not-print">
                                <button type="button" class="btn btn-primary" onclick="window.print();">สั่งพิมพ์</button>
                                <a class="btn btn-outline-primary" href="{{url('bill/update?id=')}}{{isset($bill) ? $bill->bill_id : ''}}" role="button">ไปยังบิล</a>
                                <hr>
                            </div>
                            <div class="table-responsive print-responsive">
                            <div class="print-container">

                                        <div class="d-flex justify-content-between mb-2">
                                            <div class="d-inline align-items-end bill-header">
                                                <div class="text-left d-flex align-items-end">
                                                    <div class="align-items-end p-max-40">
                                                        <h3 class="mt-1">ใบรับ-ส่งงาน</h3>
                                                        <p class="bill-num" ><span>เลขที่</span><strong>{{isset($bill) ? $bill->bill_id : ''}}</strong></p>
                                                        <p><span>วันที่</span> {{isset($bill) ? $bill->date : ''}}</p>
                                                        <p><span>นาม</span> {{isset($customer) ? $customer->name : ''}}</p>
                                                        <p><span>โทร</span> {{isset($customer) ? $customer->phone : ''}}</p>
                                                        <p><span>งาน</span> {{isset($bill) ? (
                                                                                    $bill->job_type === '1' ? 'ซ่อม' : (
                                                                                    $bill->job_type === '2' ? 'แกะสลัก' :
                                                                                    'อื่นๆ' ) ) : ''}}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-inline align-items-end bill-header">
                                                <div class="text-right d-flex align-items-end">
                                                    <div class="align-items-end p-max-60">
                                                        <img src="{{url('/').'/public/images/'}}jewerly.png">
                                                        <h4 class="mb-0"> คลีนิกอัญมณี (Jewelry Clinic)</h4><br>
                                                        <p>{{isset($branch) ? $branch->address : ''}}</p>
                                                        <p class="mb-1">โทร. <strong>{{isset($branch) ? $branch->phone : ''}}</strong> {{isset($setting) ? $setting->head_r_2 : ''}}</p>
                                                        <p><small>{{isset($setting) ? $setting->head_r_1 : ''}}</small><br>
                                                        <p><small><strong>เปิดบริการ {{isset($branch) ? $branch->date_open . ' เวลา ' . $branch->time_open : ''}}</strong></small></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @php
                                            $count_a = count($amulet)+2;
                                            $container = '100%';
                                        @endphp

                                        <div class="row justify-content-center  mb-2">
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <input type="password" class="d-none" />

                                                        <div class="table-sort-container">
                                                                <div class="table-sort-amulet print-table-container border-dark" style="width: {{$container}};" >
                                                                    <table class="table table-sort table-bordered sorted_table print-table">
                                                                        <thead class="sorted_head">
                                                                        <tr>
                                                                            <th class="static disabled text-center" width="{{ 100/$count_a }}%"></th>
                                                                            @foreach ($amulet as $i => $a)
                                                                                <th class="disabled text-center" width="{{ 100/$count_a }}%" data-amulet="{{$a->id}}" id="amulet-{{$a->id}}">
                                                                                    {{$a->name}}
                                                                                </th>
                                                                            @endforeach
                                                                            <th class="disabled text-center" width="{{ (100/$count_a)+2 }}%">ยอดเงิน</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody class="sorted_body">
                                                                        @foreach ($job as $i => $j)
                                                                            <tr>
                                                                                <th class="disabled" data-job="{{$j->id}}" id="job-{{$j->id}}" >
                                                                                    {{$j->name}}
                                                                                </th>
                                                                                @foreach ($amulet as $q => $a)
                                                                                    @php
                                                                                        isset($order) ? $jobTable = jobTable($order, $j->id, $a->id) : null;
                                                                                        isset($jobTable) ? $jobData = $jobTable['amount']. '/'.$jobTable['price'] : $jobData = '' ;
                                                                                    @endphp
                                                                                    <td class="td-job">
                                                                                        <textarea class="input-job text-area-job" name="" cols="1" rows="1"
                                                                                                  data-hook-amulet="{{$a->id}}" data-hook-job="{{$j->id}}"
                                                                                                  data-amount="" data-price="" readonly
                                                                                        >{{$jobData}}</textarea>
                                                                                        <div class="value-area-job" style="display: none">
                                                                                            <span class="badge badge-primary badge-amount"></span><br>
                                                                                            <span class="badge badge-primary badge-price"></span>
                                                                                        </div>
                                                                                    </td>
                                                                                @endforeach
                                                                                <td class="td-job">
                                                                                    <input type="text" data-hook-cost-job="{{$j->id}}" class="input-job text-right cost-job" readonly>
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

                                        @if(isset($materialData))
                                            @if($materialData != '')
                                        <div class="row">
                                            <div class="col-12">
                                                <p><strong>รายการส่วนประกอบ </strong> : <small class="small-print">{{$materialData}}</small></p>
                                            </div>
                                        </div>
                                            @endif
                                        @endif

                                        @if(isset($imagePart))
                                        <div class="row my-2">
                                            <div class="col-12">
                                                @if(count($imagePart) > 0)
                                                    <div class="d-flex justify-content-start">
                                                    @foreach ($imagePart as $img)
                                                        <div class="print-img border-dark">
                                                            <img src={{url('/').'/public/images/job/'. $img }}>
                                                        </div>
                                                    @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endif

                                        <div class="d-flex justify-content-between border border-dark p-2 mb-2">
                                            <small class="{{$payData != 0 ? 'mt-3' : 'mt-1'}}"><strong>ผู้รับเงิน</strong> ........................................</small>
                                            <p class="text-right">รวมทั้งสิ้น <strong> {{$costData}}</strong>
                                                @if($payData != 0)
                                                    <span class="cash-remain mb-0">ชำระแล้ว <strong> {{$payData}}</strong>ยอดต้องชำระ <strong> {{$remainPayData}}</strong></span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <div class="bill-footer bill-footer-box border border-dark p-2" style="width: calc(60% - 4px);">
                                                <small>{{isset($setting) ? $setting->btm_l_1 : ''}}
                                                </small>
                                                <small class="mt-1">
                                                    {{isset($setting) ? $setting->btm_l_2 : ''}}
                                                </small>
                                                <div class="d-flex justify-content-between mt-3">
                                                    <div class="d-inline align-items-end bill-header bill-footer">
                                                        <div class="text-center">
                                                            <div class="align-items-end">
                                                                <small>........................................</small>
                                                                <small>ผู้รับงาน</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-inline align-items-end bill-header bill-footer">
                                                        <div class="text-center">
                                                            <small>........................................</small>
                                                            <small>ลูกค้ารับทราบ</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bill-footer bill-footer-box border border-dark p-2" style="width: calc(40% - 4px);">
                                                <small>{{isset($setting) ? $setting->btm_r_1 : ''}}</small>
                                                <small>{{isset($setting) ? $setting->btm_r_2 : ''}}</small>
                                                <div class="text-center mt-3">
                                                    <div class="align-items-end mb-2">
                                                        <br>
                                                        <small>..................................... <strong>ผู้รับสินค้า</strong></small>
                                                    </div>
                                                    <div class="align-items-end">
                                                        <small>วันที่ .....................................</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="small small-print text-right">ผู้ออกเอกสาร {{ Auth::user()->name }} {{$getDateServer}}  {{isset($bill) ? isset($bill->bill_id) ? '(ยอมรับค่า 0)' : '' : ''}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection

@section('scripts')
    <style>
        @media print {
            @page {
                size: A5 portrait;
                margin: 0 !important;
            }
            @page:first {
                margin: 0 !important;
            }
            html,body{
                height:100%;
                width:100%;
                margin: 0 !important;
                padding:0 !important;
            }
            .container, body {
                min-width:100% !important;
                background: #fff;
            }
        }
    </style>

    <script>

        $(document).ready(function($) {

            $(function() {
                $('.text-area-job').trigger('change');
                $('.money-format').trigger('change');
            });
        //input job
        $(".text-area-job").on("click", function(){
            $('.text-area-job').trigger('blur');
        })

        $(".text-area-job").on("change", function(e) {
                e.preventDefault();
                let value = $(this).val();
                if (value !== ''){
                    $(this).parent().addClass('not-null');
                    $(this).scrollTop(0);
                } else {
                    $(this).parent().removeClass('not-null');
                }
            });

        $('.text-area-job').on('change',function () {
            let value = $(this).val().split('/');
            let element = $(this).parent().children('.value-area-job');
            let checkFloat = $.isNumeric($(this).val().replace(/\//g, ''));
            let checkVal1 = value[1] < 0 || value[1] === '' ? false : true;
            let element_ = $(this);
            if (value[0] !== "" && value[0] > 0 && checkFloat && checkVal1){
                if ($.isNumeric(value[1])){
                    if ( value[1] > 0 ){
                        element.children('span.badge-amount').text(value[0] + ' ชิ้น')
                        element.children('span.badge-price').text( 'รวม ' + formatMoney(value[1]))
                        element_.attr("data-amount",value[0]);
                        element_.attr("data-price",value[1]);
                    } else {
                        element.children('span.badge-amount').text(value[0] + ' ชิ้น')
                        element.children('span.badge-price').text( 'คิดเหมารวม' )
                        element_.attr("data-amount",value[0]);
                        element_.attr("data-price",0);
                    }
                } else {
                    element.children('span.badge-amount').text('จำนวน  ' + '1')
                    element.children('span.badge-price').text( 'รวม ' + formatMoney(value[0]))
                    element_.attr("data-amount",1);
                    element_.attr("data-price",value[0]);
                }
                element.show();
                element_.hide();
            } else {
                element.children('span.badge-amount').text('')
                element.children('span.badge-price').text('')
                element_.val('');
                element_.attr("data-amount",'');
                element_.attr("data-price",'');
            }
            sumRow(element_.data().hookJob);

        })

        function sumRow(rowId) {
            let sum = 0;
            let tr_element = $('.text-area-job[data-hook-job='+rowId+']').parent().parent().children('.not-null').not('.cost-job');
            tr_element.children('.text-area-job').each(function () {
                let value = $(this).val().split('/');
                if (value[0] !== ""){
                    if (value[1] !== undefined){
                        sum += parseFloat(value[1]);
                    } else {
                        sum += parseFloat(value[0]);
                    }
                }
            })
            if (sum > 0) {
                $('input[data-hook-cost-job='+rowId+']').val(formatMoney(sum));
            } else {
                $('input[data-hook-cost-job='+rowId+']').val("");
            }

            $(".cost-job").trigger('change');
        }
        function formatMoney(value){
            //console.log(value)
            return parseFloat(value, 10)
                .toFixed(0)
                .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")
                .toString()
        }

        })
    </script>

@stop