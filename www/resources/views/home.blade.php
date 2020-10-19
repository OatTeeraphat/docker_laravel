@extends('layouts.app')

@section('content')
    @php
        $bill = null;
        if (isset($billData[0])) {
            $bill = $billData[0] ;
            $date = explode('/',$billData[0]->date);
            $dateBc = intval($date[0]) . '/' . intval($date[1]) . '/' . strval(intval($date[2]) - 543);
            $customer = $billData[0]->customer[0];
            $order = $billData[0]->order;
            $part = $billData[0]->part;
            $role = Auth::user()->roles[0]->level;
            $user = Auth::user();
            $allow_zero = $billData[0]->allow_zero;
            //var_dump($imagePart);
            //var_dump($payment);
            //var_dump($imagePart);
            //echo $bill;
            //var_dump($gold_data);
            //dd($bill);
        }



        $role = Auth::user()->roles[0]->level;

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

        function dForm($bill){
            $role = Auth::user()->roles[0]->level;
            if (isset($bill) && isset($role)){
                if ($bill->deliver == 1 && $role < 4){
                  return 'readonly';
                } else {
                 echo null;
                }
            } else { return null; }
        }

        function dAdmin($bill){
            $role = Auth::user()->roles[0]->level;
            if (isset($bill) && isset($role)){
                if ($bill->deliver == 1){
                  return true;
                } else {
                 echo false;
                }
            } else { return false; }
        }

    @endphp

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="nav-pills-container mb-3 not-print">
                <ul class="nav nav-pills  justify-content-center justify-content-md-start">
                    <li class="nav-item mr-2">
                        <a class="nav-link active" href="{{url('bill')}}">บิลรับงาน</a>
                    </li>
                    <li class="nav-item mr-2">
                        <a class="nav-link" href="{{url('recent')}}">ดูบิลเก่า</a>
                    </li>
                    <li class="nav-item mr-2">
                        <a class="nav-link" href="{{url('summary')}}">สรุปรายวัน</a>
                    </li>
                </ul>
            </div>



            <form method="POST" id="create_main" action="{{ url('bill') }}" enctype="multipart/form-data" role="presentation" class="needs-validation" novalidate autocomplete="disabled" readonly>
                @csrf
                <input type="text" name="type" class="d-none" value="{{ $type }}" readonly>
                <input type="text" name="bill_id" class="d-none" value="{{ isset($bill) ? $bill->id : '' }}" readonly>
                <input type="text" name="customer_id" class="d-none" value="{{ isset($bill) ? $bill->customer_id : 0 }}" readonly>
                <input type="text" name="customer_old" class="d-none" value="{{ isset($bill) ? $bill->customer_id : '' }}" readonly>
                <input type="text" name="user_id" class="d-none" value="{{ Auth::user()->id }}" readonly>
                <input type="text" name="table_cost_job" class="d-none" readonly>
                <input type="text" name="table_job" class="d-none" readonly>
                <input type="text" name="table_material" class="d-none" readonly>
                <input type="text" name="old_date" class="d-none" value="{{ isset($bill) ? $bill->date : '' }}" readonly>
                <input type="text" name="image_list" class="d-none" readonly>
                <input type="text" name="total_pay" class="d-none" value="{{ isset($total_pay) ? $total_pay : '' }}" readonly>
                <input type="text" name="cash_val" class="d-none" readonly>
                <input type="text" name="cost_data" class="d-none" value="{{ isset($costData) ? $costData : '' }}" readonly>
                <input type="text" name="cost_current" class="d-none" readonly>
                <input type="text" name="gold_status" class="d-none money-format" value="{{ isset($bill->gold) ? $bill->gold : '' }}" readonly>
                <input type="text" name="gold" class="d-none money-format" value="{{ isset($gold_data->gold_1) ? $gold_data->gold_1->value : '' }}" readonly>
                <input type="text" name="gold2" class="d-none money-format" value="{{ isset($gold_data->gold_2) ? $gold_data->gold_2->value : '' }}" readonly>
                <input type="text" name="craft_id" class="d-none" readonly>
                <input type="text" name="close_bill" class="d-none" readonly>
                <input type="text" name="deliver_material" class="d-none" value="{{!isset($materialData) ?: $materialData}}" readonly>

                <div class="alert alert-success" role="alert" style="display: none; width: 100%">
                    <span class="oi oi-check"></span>
                </div>

                @if(Session::has('success'))
                    <div class="alert alert-success alert-successs" role="alert">
                        <span class="oi oi-check"></span> {{ Session::get('success') }}
                    </div>
                    <script>
                        $(".alert-successs").fadeOut(1000, function(){
                            $(".alert-successs").fadeOut(3000);
                        });
                    </script>
                @endif

                @if(isset($bill))
                    @if($bill->status == 2)
                        <div class="alert alert-danger border-danger" role="alert">
                            สถานะ : <strong>ยกเลิกบิล ({{ $bill->desc !== null ? $bill->desc : '' }})</strong><br class="d-md-none">
                            <span class="float-md-right">เลขที่บิล : <strong>{{ isset($bill) ? $bill->bill_id : '' }}</strong></span>
                        </div>
                    @elseif($bill->status == 1)
                        <div class="alert alert-success border-success" role="alert">
                            สถานะ : <strong>ปิดบิลแล้ว</strong><br class="d-md-none">
                            <span class="float-md-right">เลขที่บิล : <strong>{{ isset($bill) ? $bill->bill_id : '' }}</strong></span>
                        </div>
                    @elseif($bill->deliver == 1)
                        <div class="alert alert-primary border-primary" role="alert">
                            สถานะ : <strong>ส่งงานแล้ว</strong><br class="d-md-none">
                            <span class="float-md-right">เลขที่บิล : <strong>{{ isset($bill) ? $bill->bill_id : '' }}</strong></span>
                        </div>
                    @else()
                        <div class="alert alert-warning border-warning" role="alert">
                            สถานะ : <strong>กำลังดำเนินการ</strong><br class="d-md-none">
                            <span class="float-md-right">เลขที่บิล : <strong>{{ isset($bill) ? $bill->bill_id : '' }}</strong></span>
                        </div>
                    @endif
                @endif

            <div class="card">

                <div class="card-header">
                    <h4 class="mb-0">
                        <strong>
                            {{ isset($type) && $type == 'create'  ? 'บิลรับงาน' : 'แก้ไขบิลรับงาน'}}
                        </strong>
                    </h4>
                </div>

                <div class="card-body pb-0 pt-md-4">

                    <div class="row justify-content-center">
                        <div class="col-md-10 col-lg-8">
                            <div class="form-group row required">
                                <label for="inputdatepicker" class="col-12">วันที่ออกบิล</label>
                                <div class="col-12">
                                    <div class="input-group">
                                    <input id="inputdatepicker" name="date" readonly class="datepicker form-control {{ dForm($bill) !== null ? 'desc' : '' }} not-require" required
                                        value="{{ isset($bill) ? $bill->date : ''}}" {{ dForm($bill) !== null ? 'disabled' : '' }}
                                    >
                                        <div class="input-group-append input-append-button {{ dForm($bill) !== null ? 'desc' : '' }}">
                                            <span class="input-group-text"><span class="oi oi-calendar"></span></span>
                                        </div>
                                    </div>
                                    <span class="invalid-feedback">
                                        <strong>@if ($errors->has('date')){{ $errors->first('date')}}@endif</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-10 col-lg-8">
                            <div class="form-group row required">
                                <label for="phone" class="col-12">หมายเลขโทรศัพท์</label>
                                <div class="col-12">
                                    <input id="phone" type="phone" maxlength="12" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }} readonly-w desc" name="phone" autofocus
                                           value="{{ isset($bill) ? $customer->phone : ''}}" {{dForm($bill)}}>
                                    <span id="loader"><img src="{{ URL::asset('/public/img/loading.gif') }}"></span>
                                    <span class="invalid-feedback">
                                        <strong id="phone_error">@if ($errors->has('phone')){{ $errors->first('phone')}}@endif</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-10 col-lg-2 mt-1">
                            <div class="form-group row required">
                                <label class="col-12">ประเภทลูกค้า</label>
                                <div class="col-12">
                                    <select class="form-control disabled-w {{dForm($bill) !== null ? 'desc' : ''}}" name="customer_type" {{dForm($bill) !== null ? 'disabled' : ''}}>
                                        <option value="สด1" {{ isset($customer) ? (($customer -> customer_type === "สด1") ? 'selected' : ''):'selected'}}>สด 1</option>
                                        <option value="สด2" {{ isset($customer) ? (($customer -> customer_type === "สด2") ? 'selected' : ''):''}}>สด 2</option>
                                        <option value="สด3" {{ isset($customer) ? (($customer -> customer_type === "สด3") ? 'selected' : ''):''}}>สด 3</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10 col-lg-6 mt-1">
                            <div class="form-group row required">
                                <label for="name" class="col-12">ชื่อลูกค้า/ชื่อห้างร้าน</label>
                                <div class="col-12">
                                    <input id="name" name="name" type="text" value="{{ isset($customer) ? $customer->name : '' }}" class="form-control{{ $errors->has('customer_name') ? ' is-invalid' : '' }} secure readonly-w {{dForm($bill) !== null ? 'desc' : ''}}" required autofocus {{dForm($bill)}} >
                                    <input id="username" name="username" type="text" value="" class="disapear"  >
                                    <span id="loader-name"><img src="{{ URL::asset('/public/img/loading.gif') }}"></span>
                                    <button type="button" class="badge badge-primary btn" id="search-name" style="display: none;">ค้นหา</button>
                                    @if(!isset($bill) or (isset($bill) and $bill->deliver === 0) or (isset($bill) and $role === 4))
                                    <button type="button" class="badge badge-danger btn" id="search-name-clear" style="display: none;">ลบชื่อ</button>
                                    @endif
                                    <div class="drop-name dropdown-menu mt-1 border p-1" id="drop-name-item">
                                    </div>
                                    <div class="drop-name dropdown-menu mt-1 border p-1" id="drop-name-notfound">
                                        <h5 class="m-0 p-2"><strong>ไม่มีผลการค้นหา</strong></h5>
                                    </div>
                                    <span class="invalid-feedback">
                                        <strong>@if ($errors->has('customer_name')){{ $errors->first('customer_name')}}@endif</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-10 col-lg-8">
                            <div class="form-check mt-2 mb-1">
                                <input class="form-check-input" type="radio" name="order_type" id="order_type1" value="1"
                                        {{ isset($bill) ? (($bill -> job_type === "1") ? 'checked' : ''):'checked'}} {{dForm($bill) !== null ? 'disabled' : ''}}>
                                <label class="form-check-label" for="order_type1">
                                    งานซ่อม
                                </label>
                            </div>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="radio" name="order_type" id="order_type2" value="2"
                                        {{ isset($bill) ? (($bill -> job_type === "2") ? 'checked' : ''):''}} {{dForm($bill) !== null ? 'disabled' : ''}}>
                                <label class="form-check-label" for="order_type2">
                                    งานแกะสลัก
                                </label>
                            </div>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="radio" name="order_type" id="order_type3" value="3"
                                        {{ isset($bill) ? (($bill -> job_type === "3") ? 'checked' : ''):''}} {{dForm($bill) !== null ? 'disabled' : ''}}>
                                <label class="form-check-label" for="order_type3">
                                    อื่นๆ
                                </label>
                            </div>
                        </div>
                    </div>

                    @php
                        $count_a = count($amulet)+2;
                        $container = '100%';
                    @endphp

                    <div class="row justify-content-center">
                        <div class="col-12 col-lg-10 mt-3 mb-2">
                            <div class="form-group row required">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-12 col-md-6 px-0">
                                            <label for="table_job" class="col-12">รายการงานซ่อม</label>
                                        </div>
                                        <div class="col-12 col-md-6 px-0">
                                        @if($role == 4)
                                            <div class="form-check form-check-inline float-md-right">
                                                <input class="form-check-input" type="checkbox" id="allow_zero" name="allow_zero" value="1" {{isset($bill) ? ($allow_zero == 1 ? 'checked': '') : ''}}>
                                                <label class="form-check-label n-require" for="allow_zero">ยอมรับค่า 0 ได้</label>
                                            </div>
                                        @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <input type="password" class="d-none" />
                                    <div class="table-responsive">
                                        <div class="table-sort-container">
                                            <div class="table-sort-amulet" style="width: {{$container}};" >
                                                <table class="table table-sort table-bordered sorted_table">
                                                    <thead class="sorted_head">
                                                    <tr>
                                                        <th class="static disabled" width="{{ 100/$count_a }}%"></th>
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
                                                                    <textarea class="input-job text-area-job" name="" cols="1" rows="2"
                                                                              data-hook-amulet="{{$a->id}}" data-hook-job="{{$j->id}}"
                                                                              data-amount="" data-price="" {{dForm($bill)}}
                                                                    >{{$jobData}}</textarea>
                                                                    <div class="value-area-job" style="display: none">
                                                                        <span class="badge badge-primary badge-amount"></span>
                                                                        <span class="badge badge-primary badge-price"></span>
                                                                    </div>
                                                                </td>
                                                            @endforeach
                                                            <td class="td-job summary">
                                                                <input type="text" data-hook-cost-job="{{$j->id}}" class="input-job text-right cost-job" readonly>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <span class="invalid-feedback"  id="table_error" style="display: none;">
                                        <strong>กรุณาระบุ ราคาและงานซ่อมให้ครบถ้วน</strong>
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-10 col-lg-8">
                            <div class="form-group row">
                                <label for="service_cost" class="col-12">รวมค่าบริการ</label>
                                <div class="col-12">
                                    <input id="service_cost" type="text" maxlength="12" class="cost form-control{{ $errors->has('service_cost') ? ' is-invalid' : '' }} money-format" name="service_cost" value="0.00" readonly>
                                    <span class="invalid-feedback">
                                            <strong>@if ($errors->has('service_cost')){{ $errors->first('service_cost')}}@endif</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-10 col-lg-8">
                            <div class="form-group row">
                                <label for="" class="col-12">รายการส่วนประกอบ</label>
                                <div class="col-12 col-sm-8 col-md-6">
                                   <div class="table-responsive">
                                        <table class="table table-bordered table-sort">
                                            <thead>
                                            <tr>
                                                <th class="disabled" width="50%">
                                                    รายการ
                                                </th>
                                                <th class="disabled">
                                                    ราคา
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($material as $m)
                                                @php
                                                    isset($part) ? $materialTable = materialTable($part, $m->id) : null ;
                                                    isset($materialTable) ? $materialData = $materialTable['price'] : $materialData = '' ;
                                                @endphp
                                                <tr>
                                                    <th class="disabled">{{ $m->name }}</th>
                                                    <td class="td-job">
                                                        <input type="text" data-hook-material="{{ $m->id }}" class="input-job money-format text-right material-job" value="{{$materialData}}" {{dForm($bill)}}>
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

                    @if(isset($type) && $type == 'update')
                    <div class="row justify-content-center">
                        <div class="col-md-10 col-lg-8 mt-2">
                            <div class="form-group row">
                                <div class="col-12">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" {{ isset($bill->gold) ? ( $bill->gold == 0 ? 'checked' : '' ) : ''}}
                                        id="gold_input_check" name="gold_input_check" {{isset($bill) && $bill->status == 1 && $role !== 4 ? 'disabled' : ''}}>
                                        <label class="custom-control-label" for="gold_input_check"><strong>บิลนี้ไม่ใช้ทอง</strong></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10 col-lg-8" id="gold_input">
                            <div class="form-group row">
                                <label for="service_cost" class="col-12">ยอดใช้ทอง</label>
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-12 col-md-9 col-lg-9">
                                            <input id="gold_value" type="text" maxlength="12" class="cost form-control money-format" name="gold_value"
                                                    readonly>
                                            <span class="invalid-feedback">
                                        </span>
                                        </div>
                                        <div class="col-12 col-md-3 col-lg-3 mt-2 mt-md-0">
                                            @if(isset($bill) && $bill->status == 1 && $role !== 4)
                                            <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" id="modalGold_" data-target="#modalGold">
                                                ดูยอดทอง
                                            </button>
                                            @else
                                            <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" id="modalGold_" data-target="#modalGold">
                                                ลงยอดทอง
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    @endif

                    <div class="row justify-content-center">
                        <div class="col-md-10 col-lg-8">
                            <div class="form-group row">
                                <label for="material_cost" class="col-12">รวมค่าส่วนประกอบ</label>
                                <div class="col-12">
                                    <input id="material_cost" type="text" maxlength="12" class="cost form-control{{ $errors->has('service_cost') ? ' is-invalid' : '' }} money-format" name="material_cost" value="0.00" readonly>
                                    <span class="invalid-feedback">
                                            <strong id="material_cost">@if ($errors->has('material_cost')){{ $errors->first('material_cost')}}@endif</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-10 col-lg-8">
                            <div class="form-group row">
                                <label for="total" class="col-12">รวมเป็นเงินทั้งสิ้น</label>
                                <div class="col-12">
                                    <input id="total" type="text" maxlength="12" class="cost form-control{{ $errors->has('total') ? ' is-invalid' : '' }} money-format" name="total" value="0.00" readonly>
                                    <span class="invalid-feedback">
                                            <strong id="phone_error">@if ($errors->has('total')){{ $errors->first('total')}}@endif</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-10 col-lg-8">
                            <div class="form-group row">
                                <label for="service_cost" class="col-12">รับชำระแล้ว</label>
                                <div class="col-12">
                                    <div class="row">
                                    <div class="col-12 col-md-9 col-lg-9">
                                        <input id="cash" type="text" maxlength="12" class="cost form-control{{ $errors->has('cash') ? ' is-invalid' : '' }} money-format" name="cash"
                                               value="{{ isset($bill) ? $bill->cash : '0.00'}}" readonly>
                                        <span class="invalid-feedback">
                                        </span>
                                    </div>
                                    <div class="col-12 col-md-3 col-lg-3 mt-2 mt-md-0">
                                        @if(isset($bill) && $bill->status == 1 && $role !== 4)
                                            <button type="button" class="btn btn-primary btn-lg" id="modalCash-btn" data-toggle="modal" data-target="#modalPayHistory">
                                                ประวัติชำระ
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-primary btn-lg" id="modalCash-btn" data-toggle="modal" data-target="#modalCash">
                                                ชำระเงิน
                                            </button>
                                        @endif
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-12 col-md-10 col-lg-8 mt-1 mt-md-3 mt-lg-2">
                            <div class="form-group row">
                                <div class="col-12">
                                    <label for="image-file">
                                        รูปภาพงานซ่อม
                                    </label>
                                    <button type="button" id="image_file_trigger" class="btn btn-lg btn-primary col-12 col-md-2 ml-md-3" {{dForm($bill) !== null ? 'disabled' : 'primary'}}>เพิ่มรูป</button>

                                </div>
                                <div class="col-12">
                                </div>
                                <div class="col-12">
                                    <div class="form-group text-center">
                                        <div class="file-loading">
                                            <input id="image-file" type="file" name="file" accept="image/*" multiple {{dForm($bill)}}>
                                        </div>
                                        <span class="invalid-feedback">
                                        <strong id="gallery_error">@if ($errors->has('service_cost')){{ $errors->first('service_cost')}}@endif</strong>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card mt-3 mb-1">
                <div class="card-body" >
                    <div class="row justify-content-center" id="btm-button">
                        <div class="col-12 text-center">
                        @if (Request::is('bill'))
                            <button type="button" class="btn btn-primary btn-lg mr-2 col-12 col-md-4 col-lg-2 mb-2 mb-md-0" id="btn-submit">เปิดบิลรับงาน</button>
                            <button type="button" class="btn btn-secondary btn-lg col-12 col-md-4 col-lg-2" onclick="location.reload();">รีเซ็ตข้อมูล</button>
                        @elseif(($bill->status == 1 or $bill->status == 2) and $role === 4)
                            <button type="button" class="btn btn-primary btn-lg mr-2 col-12 col-md-4 col-lg-2 mb-2 mb-lg-0" id="btn-submit" {{ dAdmin($bill) and $role === 4 ? 'disabled' : ''}}>บันทึกแก้ไข</button>
                            <button type="button" class="btn btn-primary btn-lg mr-2 col-12 col-md-4 col-lg-2 mb-2 mb-lg-0" id="btn-print" data-href="{{url('/recent/bill?id=')}}{{ isset($bill) ? $bill->bill_id : '' }}" >พิมพ์บิลนี้</button>
                        @elseif($bill->status == 1 or $bill->status == 2)
                            <button type="button" class="btn btn-primary btn-lg mr-2 col-12 col-md-4 col-lg-2 mb-2 mb-lg-0" id="btn-print" data-href="{{url('/recent/bill?id=')}}{{ isset($bill) ? $bill->bill_id : '' }}" >พิมพ์บิลนี้</button>
                        @else
                            <button type="button" class="btn btn-primary btn-lg mr-2 col-12 col-md-4 col-lg-2 mb-2 mb-lg-0" id="btn-submit" {{ dAdmin($bill) && $role < 3 ? 'disabled' : ''}}>บันทึกแก้ไข</button>
                            <button type="button" class="btn btn-primary btn-lg mr-2 col-12 col-md-4 col-lg-2 mb-2 mb-lg-0" id="btn-modal-deliver" {{dAdmin($bill) ? 'disabled' : ''}}>ส่งงานลูกค้า</button>
                            <button type="button" class="btn btn-primary btn-lg mr-2 col-12 col-md-4 col-lg-2 mb-2 mb-lg-0" id="btn-close-success" {{dAdmin($bill) ? '' : 'disabled'}}>ปิดบิล</button>
                            <button type="button" class="btn btn-primary btn-lg mr-2 col-12 col-md-4 col-lg-2 mb-2 mb-lg-0" id="btn-print" data-href="{{url('/recent/bill?id=')}}{{ isset($bill) ? $bill->bill_id : '' }}" >พิมพ์บิลนี้</button>
                            @if ($bill->deliver == 1 and $role > 2)
                            <button type="button" class="btn btn-danger btn-lg mr-2 col-12 col-md-auto mb-2 mb-md-0" id="btn-modal-billvoid" {{dAdmin($bill) ? '' : 'disabled'}}>
                                <span class="d-md-none">ยกเลิกบิล</span>
                                <span class="oi oi-trash d-none d-md-inline pl-1"></span>
                            </button>
                            @endif

                        @endif
                        </div>
                    </div>
                    <div id="ajax_load" class="row justify-content-center"  style="display: none">
                        <img src="{{url('/')}}/public/img/loading-lg.gif" alt=""><h4 class="mt-1 mx-2 mb-0  text-muted">กำลังทำรายการ</h4>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalCash" tabindex="-1" role="dialog" aria-labelledby="modalCash" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                <div class="modal-dialog modal-lg modal-cash" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="row justify-content-center">
                                    <div class="col-md-11">
                                        <div class="alert alert-danger mt-1 mt-md-3 mb-0" id="alert-payment" role="alert" style="display: none; width: 100%">
                                            <span class="oi oi-check"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-md-5 order-md-2 mt-md-3 mt-1 mb-0">
                                        <h4 class="d-flex justify-content-between align-items-center mb-3">
                                            <span>ยอดรวม</span>
                                            <span class="text-primary" id="pay_total_"><h4 class="mb-0" style="font-size: 1.2em"><strong></strong></h4></span>
                                        </h4>
                                        <ul class="list-group mb-3">
                                            <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                <h6 class="my-1">ค่าบริการ</h6>
                                                <span class="text-muted" id="pay_service">0.00</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                <h6 class="my-1">ค่าส่วนประกอบ</h6>
                                                <span class="text-muted" id="pay_material">0.00</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between bg-light">
                                                <span>รวมทั้งสิ้น</span>
                                                <strong id="pay_total">0.00</strong>
                                            </li>
                                        </ul>
                                        <ul class="list-group mb-3">
                                            <li class="list-group-item d-flex justify-content-between bg-light">
                                                <span class="text-success">
                                                    ชำระแล้ว
                                                    @if(isset($payment) && $role > 2)
                                                    <button type="button" class="btn btn-link py-0 px-1" id="modalPayHistory-btn" data-toggle="modal" data-target="#modalPayHistory" data-dismiss="modal">
                                                        <span class="oi oi-external-link text-success"></span>
                                                    </button>
                                                    @endif
                                                </span>
                                                <strong id="payment-method-total">0.00</strong>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between bg-light">
                                                <span>ค้างชำระ</span>
                                                <strong id="payment-remain">0.00</strong>
                                            </li>
                                        </ul>
                                        <small class="text-danger">* รายการชำระจะบันทึก หลังจากบันทึกข้อมูลบิลสำเร็จ</small>

                                    </div>
                                    <div class="col-md-6 order-md-1 mt-md-3 mt-3">
                                        <h4 class="d-flex justify-content-between align-items-center mb-3">
                                            ชำระด้วย
                                        </h4>
                                        <hr class="mb-4">
                                        <div class="form-group input-group mb-4">
                                            <div class="input-group-prepend bg-light">
                                                <span class="input-group-text">เงินสด</span>
                                            </div>
                                            <input type="text" class="money-format form-control payment-method not-require text-right font-weight-bold" name="pay_cash" class="form-control">
                                        </div>

                                        <div class="form-group input-group mb-4">
                                            <div class="input-group-prepend bg-light">
                                                <span class="input-group-text">บัตรเครดิต</span>
                                            </div>
                                            <input type="text" class="money-format form-control payment-method not-require text-right font-weight-bold" name="pay_credit" class="form-control">
                                        </div>

                                        <div class="form-group input-group mb-4">
                                            <div class="input-group-prepend bg-light">
                                                <span class="input-group-text">Voucher</span>
                                            </div>
                                            <input type="text" class="money-format form-control payment-method not-require text-right font-weight-bold" name="pay_voucher" class="form-control">
                                        </div>

                                        <div class="form-group input-group mb-4">
                                            <div class="input-group-prepend bg-light">
                                                <span class="input-group-text">คูปอง</span>
                                            </div>
                                            <input type="text" class="money-format form-control payment-method not-require text-right font-weight-bold" name="pay_coupon" class="form-control">
                                        </div>

                                        <div class="form-group input-group mb-4">
                                            <div class="input-group-prepend bg-light">
                                                <span class="input-group-text">QR code</span>
                                            </div>
                                            <input type="text" class="money-format form-control payment-method not-require text-right font-weight-bold" name="pay_online" class="form-control">
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="button" class="btn btn-primary btn-lg" id="btn-add-payment">ทำรายการต่อ</button>
                                <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal" id="payment-dissmiss">ยกเลิก</button>
                            </div>
                        </div>
                    </div>
            </div>

            <div class="modal fade" id="modalGold" tabindex="-1" role="dialog" aria-labelledby="modalGold" aria-hidden="true" data-keyboard="false" data-backdrop="static">

                    <div class="modal-dialog modal-pay modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-body pt-4">
                                <div class="row justify-content-center my-md-2 mb-2">
                                    <div class="col-12 col-md-11">
                                        <h4>ยอดใช้ทอง (ช่างคนที่ 1)</h4>
                                        <hr>
                                        <div class="form-group row required">
                                            <div class="col-12 col-md-6">
                                                <label for="gold">น้ำหนักทองที่ใช้</label>
                                                <div class="input-group input-group-lg">
                                                    <input type="text" class="money-format form-control not-require text-right sucess-element" name="gold_" id="gold" class="form-control" value="{{ isset($gold_data->gold_1) ? $gold_data->gold_1->value : '' }}"
                                                    {{isset($bill) && $bill->status == 1 && $role !== 4 ? 'readonly' : ''}}>
                                                    <input type="text" class="d-none" name="gold_id_" value="{{ isset($gold_data->gold_1) ? $gold_data->gold_1->id : '' }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">กรัม</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="craft_id">ช่างทอง</label>
                                                <select class="custom-select custom-select-lg sucess-element" id="craft_id_" name="craft_id_"
                                                    {{isset($bill) && $bill->status == 1 && $role !== 4 ? 'disabled' : ''}}>
                                                    <option value="" selected disabled hidden >กรุณาระบุช่างทอง</option>
                                                    @if(isset($craft))
                                                        @foreach($craft as $l)
                                                            <option value="{{$l['id']}}" {{ isset($gold_data->gold_1) ?
                                                                                         ( $gold_data->gold_1->craft_id == $l['id'] ? 'selected' : '')
                                                                                         : '' }}
                                                            >
                                                                {{$l['name']}}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option value="" hidden disabled>ไม่พบช่างทองในสาขา</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-12 col-md-11">
                                        <h4>ยอดใช้ทอง (ช่างคนที่ 2)
                                            <button type="button" class="badge badge-primary border-0 float-right mb-0 pt-0 mt-2 {{isset($gold_data->gold_2) ? 'd-none' : ''}} cursor-pointer" id="gold_second_toggle"
                                                    {{isset($bill) && $bill->status == 1 && $role !== 4 ? 'disabled' : ''}}>เพิ่มช่าง 2</button>
                                            <button type="button" class="badge badge-danger border-0 float-right mb-0 pt-0 mt-2 {{isset($gold_data->gold_2) ?: 'd-none'}} cursor-pointer" id="gold_second_toggle_"
                                                    {{isset($bill) && $bill->status == 1 && $role !== 4 ? 'disabled' : ''}}>ลบช่าง 2</button>
                                        </h4>
                                        <hr>
                                        <div class="form-group row required {{isset($gold_data->gold_2) ?: 'd-none'}}" id="gold_second">
                                            <div class="col-12 col-md-6">
                                                <label for="gold2">น้ำหนักทองที่ใช้</label>
                                                <div class="input-group input-group-lg">
                                                    <input type="text" class="money-format form-control not-require text-right sucess-element gold" name="gold_2" class="form-control" value="{{ isset($gold_data->gold_2) ? $gold_data->gold_2->value : '' }}"
                                                            {{isset($bill) && $bill->status == 1 && $role !== 4 ? 'readonly' : ''}}>
                                                    <input type="text" class="d-none" name="gold_id_2" value="{{ isset($gold_data->gold_2) ? $gold_data->gold_2->id : '' }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">กรัม</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="craft_id">ช่างทอง</label>
                                                <select class="custom-select custom-select-lg sucess-element" id="craft_id_2" name="craft_id_2"
                                                        {{isset($bill) && $bill->status == 1 && $role !== 4 ? 'disabled' : ''}}>
                                                    <option value="" selected disabled hidden >กรุณาระบุช่างทอง</option>
                                                    @if(isset($craft))
                                                        @foreach($craft as $l)
                                                            <option value="{{$l['id']}}" {{ isset($gold_data->gold_2) ?
                                                                                         ( $gold_data->gold_2->craft_id == $l['id'] ? 'selected' : '')
                                                                                         : '' }}
                                                            >
                                                                {{$l['name']}}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option value="" hidden disabled>ไม่พบช่างทองในสาขา</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="button" class="btn btn-primary btn-lg mr-2" id="btn-gold" data-dismiss="modal" disabled>ทำรายการต่อ</button>
                                <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">ย้อนกลับ</button>
                            </div>
                        </div>
                    </div>

                </div>

            </form>

        </div>
    </div>
    <div class="row justify-content-center">

    </div>
</div>

<button type="button" class="d-none" id="myModal-btn" data-toggle="modal" data-target="#myModal">
</button>

<button type="button" class="d-none" id="myModal2-btn" data-toggle="modal" data-target="#myModal2">
</button>

<button type="button" class="d-none" id="modalError-btn" data-toggle="modal" data-target="#modalError">
</button>

<button type="button" class="d-none" id="modalDeliver-btn" data-toggle="modal" data-target="#modaldeliver">
</button>

<button type="button" class="d-none" id="modalDeliverError-btn" data-toggle="modal" data-target="#modaldeliverError">
</button>

<button type="button" class="d-none" id="modalPrint-btn" data-toggle="modal" data-target="#modalPrint">
</button>

<button type="button" class="d-none" id="modalGold-btn" data-toggle="modal" data-target="#modalGold">
</button>

<button type="button" class="d-none" id="modalGoldError-btn" data-toggle="modal" data-target="#modalGoldError">
</button>

<button type="button" class="d-none" id="modalPayError-btn" data-toggle="modal" data-target="#modalPayError">
</button>

<button type="button" class="d-none" id="modalBillVoid-btn" data-toggle="modal" data-target="#modalBillVoid">
</button>

<button type="button" class="d-none" id="modalBillVoidError-btn" data-toggle="modal" data-target="#modalBillVoidError">
</button>

<button type="button" class="d-none" id="modalGoldVoid-btn" data-toggle="modal" data-target="#modalGoldVoid">
</button>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModal" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-no-member" role="document">
        <div class="modal-content">
            <div class="modal-body pt-4">
                <h2 class="text-center">ไม่พบข้อมูลลูกค้า</h2>
                <p class="text-center">ต้องการสร้างข้อมูลลูกค้าใหม่หรือไม่</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary btn-lg" id="modal-btn-new">สร้างใหม่</button>
                <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">ยังไม่สร้าง</button>
            </div>
        </div>
    </div>
    <div class="modal-dialog modal-lg modal-add-member" role="document" style="max-width: 700px; display:none;">
        <form method="POST" id="customer_form" class="customer-validation" enctype="multipart/form-data" autocomplete="off">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-12">
                    <h3 class="text-center mb-0">เพิ่มข้อมูลลูกค้า</h3>
                </div>
            </div>
            <input type="text" name="customer_type" class="d-none" value="customer">
            <div class="modal-body pt-4">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-danger" role="alert" style="display: none; width: 100%">
                                <span class="oi oi-check"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row required">
                        <label for="customer_type" class="col-md-4 col-form-label text-md-right">ประเภทลูกค้า</label>
                        <div class="col-md-6">
                            <select class="form-control" name="customer_type">
                                <option value="สด1" selected>สด 1</option>
                                <option value="สด2">สด 2</option>
                                <option value="สด3">สด 3</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row required">
                        <label for="name_" class="col-md-4 col-form-label text-md-right">ชื่อลูกค้า/ชื่อห้างร้าน</label>
                        <div class="col-md-6">
                            <input id="name_" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus>
                            <span class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                    </div>

                    <div class="form-group row" id="address_group">
                        <label for="address" class="col-md-4 col-form-label text-md-right">ที่อยู่</label>
                        <div class="col-md-6">
                            <textarea id="address" type="text" class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }} not-require" name="address" autofocus rows="3"></textarea>

                            @if ($errors->has('address'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('address') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row required">
                        <label for="phone_" class="col-md-4 col-form-label text-md-right">หมายเลขโทรศัพท์</label>
                        <div class="col-md-6">
                            <input id="phone_" type="text" maxlength="12" class="disabled-w form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ old('phone') }}" autofocus readonly>
                            <span class="invalid-feedback">
                               <strong id="phone_error">@if ($errors->has('phone')){{ $errors->first('phone')}}@endif</strong>
                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="line" class="col-md-4 col-form-label text-md-right">Line ID</label>
                        <div class="col-md-6">
                            <input id="line" type="text" class="form-control{{ $errors->has('line') ? ' is-invalid' : '' }} not-require" name="line" value="{{ old('line') }}" autofocus>

                            @if ($errors->has('line'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('line') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="line" class="col-md-4 col-form-label text-md-right"></label>
                        <div class="col-md-6">
                            <div class="custom-control custom-checkbox mt-1 mb-0 mr-sm-2">
                                <input type="checkbox" class="custom-control-input" checked  id="customControlInline" name="already_used" value="1" >
                                <label class="custom-control-label" for="customControlInline">ลูกค้าใหม่ [ยังไม่เคยใช้บริการ]</label>
                            </div>
                        </div>
                    </div>


            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary btn-lg" id="modal-btn-create">ยืนยันการเพิ่มข้อมูล</button>
                <button type="button" class="btn btn-secondary btn-lg" id="modal-btn-notnow" data-dismiss="modal">ยังไม่สร้าง</button>
            </div>
        </div>
        </form>
    </div>
</div>

<div class="modal fade" id="myModal2" tabindex="0" role="dialog" aria-labelledby="myModal2" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body pt-4">
                <h2 class="text-center">กำลังสร้างบิลรับงาน</h2>
                <p class="text-center">กรุณารอสักครู่ กำลังอัพโหลดรูปภาพ</p>
            </div>
            <div class="modal-footer justify-content-center">
                <div class="progress" style="height: 20px; width: 100%">
                    <div id="upload-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalError" tabindex="-1" role="dialog" aria-labelledby="modalError" aria-hidden="true">
    <div class="modal-dialog modal-err" role="document">
        <div class="modal-content">
            <div class="modal-body pt-4">
                <h2 class="text-center">มีบางอย่างไม่ถูกต้อง</h2>
                <p class="text-center">กรุณาตรวจสอบความครบถ้วนของข้อมูล</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-danger btn-lg" data-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modaldeliver" tabindex="-1" role="dialog" aria-labelledby="modaldeliver" aria-hidden="true" data-keyboard="false" data-backdrop="static">

    <div class="modal-dialog modal-lg modal-deliver" role="document" style="max-width: 1000px;">
        <div class="modal-content">
            <div class="modal-body pt-4">

                <div class="row justify-content-center">
                    <div class="col-12 col-md-10">
                        <h4 class="text-center text-danger">ส่งงานคืนลูกค้าให้ครบตามจานวนที่รับมาก่อน พร้อมให้ลูกค้าเซ็นต์รับสินค้าในบิลแล้วจึง
                            เช็คที่ช่องส่งคืนครบแล้ว (ตรวจสอบให้แน่ใจ เพราะไม่สามารถกลับมาแก้ไขได้)</h4>
                        <p class="text-center mb-0">เลขที่บิล : <strong>{{ isset($bill) ? $bill->bill_id : '' }}</strong></p>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-12 col-lg-11 mt-3 pb-0">
                        <div class="form-group row">
                            <div class="col-12">
                                <input type="password" class="d-none" />
                                <div class="table-responsive">
                                    <div class="table-sort-container">
                                        <div class="table-sort-amulet" style="width: {{$container}};" >
                                            <table class="table table-sort table-bordered sorted_table confirm_table">
                                                <thead class="sorted_head">
                                                <tr>
                                                    <th class="static disabled" width="{{ 100/$count_a }}%"></th>
                                                    @foreach ($amulet as $i => $a)
                                                        <th class="disabled" width="{{ 100/$count_a }}%" data-amulet="{{$a->id}}" id="amulet-{{$a->id}}">
                                                            {{$a->name}}
                                                        </th>
                                                    @endforeach
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
                                                                    <textarea class="input-job text-area-job" name="" cols="1" rows="2" readonly>{{$jobData}}</textarea>
                                                                <div class="value-area-job confirm_job" style="display: none">
                                                                    <span class="badge badge-primary badge-amount"></span><br>
                                                                    <span class="badge badge-primary badge-price"></span>
                                                                </div>
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-2"><strong id="deliver-material_" style="display: none">ส่วนประกอบ <span id="deliver-material"></span></strong></p>
                                <h3 class="text-center mb-0 ">รวมทั้งสิ้น <strong><span id="deliver-cost"></span> บาท</strong></h3>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row justify-content-center">
                    @if(isset($imagePart))
                        @if(count($imagePart) > 0)
                    <div class="col-12 col-lg-11 mt-1 mb-2 mb-md-3">
                        <div class="table-responsive text-md-center">
                            <div class="img-deliver-container" style="min-width: {{194*count($imagePart)}}px">
                            @foreach ($imagePart as $img)
                                <div class="img-deliver" >
                                    <img src="{{url('/').'/public/images/job/'. $img }}"  alt="">
                                </div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                        @endif
                    @endif
                </div>

                <div class="row justify-content-center">
                    <div class="custom-control custom-checkbox mr-sm-2">
                        <input type="checkbox" class="custom-control-input" id="customControlAutosizing">
                        <label class="custom-control-label" for="customControlAutosizing">ส่งชิ้นงานคืนลูกค้าครบแล้ว</label>
                    </div>
                </div>

            </div>
            <div class="modal-footer justify-content-center">
                <form method="POST" id="update_deliver" action="{{ url('bill/update/deliver') }}" autocomplete="off">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg mr-2" id="btn-conferm" disabled>ยืนยัน ส่งชิ้นงานคืนลูกค้า</button>
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">ยกเลิก</button>
                    <input type="text" name="bill_id" class="d-none" value="{{ isset($bill) ? $bill->id : '' }}" readonly>
                </form>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="modaldeliverError" tabindex="-1" role="dialog" aria-labelledby="modaldeliverError" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-err" role="document">
            <div class="modal-content">
                <div class="modal-body pt-4">
                    <h2 class="text-center">มีการแก้ไขบิล</h2>
                    <p class="text-center">กรุณาบันทึกการแก้ไข ก่อนส่งงานลูกค้า</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary btn-lg" id="btn-modal-err-deliver" data-dismiss="modal">บันทึกการแก้ไข</button>
                    <button type="button" class="btn btn-secondary btn-lg"  data-dismiss="modal">ยกเลิก</button>
                </div>
            </div>
        </div>
</div>

<div class="modal fade" id="modalPrint" tabindex="-1" role="dialog" aria-labelledby="modalPrint" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-err" role="document">
            <div class="modal-content">
                <div class="modal-body pt-4">
                    <h2 class="text-center">มีการแก้ไขบิล</h2>
                    <p class="text-center">กรุณาบันทึกการแก้ไข ก่อนสั่งพิมพ์</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary btn-lg" id="btn-modal-print" data-dismiss="modal">บันทึกการแก้ไข</button>
                    <button type="button" class="btn btn-secondary btn-lg"  data-dismiss="modal">ยกเลิก</button>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="modalPayHistory" tabindex="-1" role="dialog" aria-labelledby="modalPayHistory" aria-hidden="true" data-keyboard="false" data-backdrop="static">

    @if(isset($payment))

    <div class="modal-dialog modal-lg modal-cash" role="document">
        <div class="modal-content">
            <div class="modal-body" style="min-height: 450px">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="alert alert-danger mt-md-3 mt-3 mb-0" id="alert-payment" role="alert" style="display: none; width: 100%">
                            <span class="oi oi-check"></span>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-11 order-md-1 mt-md-4 mt-0">
                        <h4 class="d-flex justify-content-between align-items-center mb-3">
                            ประวัติการชำระเงิน
                        </h4>
                        <hr class="mb-4">
                        @if(count($payment) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered" style="min-width: 650px">
                            <thead>
                            <tr>
                                <th scope="col" class="disabled" width="30">#</th>
                                <th scope="col" class="disabled" width="70">วันที่ชำระ</th>
                                <th scope="col" class="disabled" width="100">ยอด</th>
                                <th scope="col" class="disabled" width="35">โดย</th>
                                <th scope="col" class="disabled" >หมายเหตุ</th>
                                @if(isset($bill) && ( $bill->status == 0 || $role == 4 ))
                                <th scope="col" class="disabled" width="30">ยกเลิก</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($payment as $i => $p)
                                <tr id="row-{{$i}}">
                                    <th scope="row" class="disabled"><small>{{$i+1}}</small></th>
                                    <td scope="row"><small>{{$p->date}}</small></td>

                                    <td class="text-right money-format {{ isset($p->cause) ? 'text-danger' : 'text-sucess'}}">{{$p->value}}</td>
                                    @switch($p->method)
                                        @case('cash')<td><small>เงินสด</small></td>@break
                                        @case('credit')<td><small>บัตรเครดิต</small></td>@break
                                        @case('online')<td><small>ออนไลน์</small></td>@break
                                        @case('coupon')<td><small>คูปอง</small></td>@break
                                        @case('voucher')<td><small>Voucher</small></td>@break
                                        @default<span>-</span>
                                    @endswitch
                                    <td>
                                        <div class="badge {{ isset($p->cause)? 'badge-danger' : 'badge-primary' }}" style="width: 47px">{{ isset($p->cause)? ' ยกเลิก ': 'รับชำระ ' }}</div>
                                        <small>{{ isset($p->cause)? $p->user_void  : $p->user_recive }}</small>
                                        @if(isset($p->cause))
                                        <small> ({{ isset($p->cause) ? $p->cause : ''}})</small>
                                        @endif
                                    </td>
                                    @if(isset($bill) && ( $bill->status == 0 || $role == 4 ))
                                    <td class="text-center"><a href="" id="" class="badge badge-danger btn-void {{ isset($p->cause) ? 'd-none' : ''}}" data-toggle="modal" data-dismiss="modal" data-target="#modalPayVoid"  data-id='{{$p->id}}' data-bill="{{ isset($bill) ? $bill->id : '' }}" data-path='{{ url('bill/payment/delete?id=') }}'>
                                            <span class="oi oi-x mb-1"></span>
                                        </a>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                            </div>
                            @else
                            <div class="row justify-content-center">
                                <h4 class="text-muted">ไม่มีประวัติการชำระเงิน</h4>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal" id="payment-dissmiss">ย้อนกลับ</button>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="modal fade" id="modalPayVoid" tabindex="-1" role="dialog" aria-labelledby="modalPayVoid" aria-hidden="true">
    <form method="POST" action="{{ url('bill/payment/delete') }}" autocomplete="off">
        @csrf
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body pt-4">
                    <h2 class="text-center">ยกเลิกรายการชำระเงิน<span id="name"></span></h2>
                    <p class="text-center">รายการนี้จะไม่ถูกนำไปรวมยอดเงิน</p>
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-10">
                            <div class="form-group row required">
                                <div class="col-12">
                                    <label for="pay_cause">เหตุผลการยกเลิก</label>
                                        <input type="text" class="form-control not-require" name="pay_cause" id="pay_cause" class="form-control">
                                        <input type="text" class="d-none" name="id" value="{{ isset($bill) ? $bill->id : '' }}" readonly>
                                        <input type="text" class="d-none" name="t_id" value="" readonly>
                                        <input type="text" name="user_id" class="d-none" value="{{ Auth::user()->id }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-danger text-white btn-lg" id="btn-submit-void" role="button" disabled>ยืนยัน ยกเลิกยอดชำระ</button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="modalBillVoid" tabindex="-1" role="dialog" aria-labelledby="modalBillVoid" aria-hidden="true">
        <form method="POST" action="{{ url('bill/delete') }}" autocomplete="off">
            @csrf
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body pt-4">
                        <h2 class="text-center">ยกเลิกบิล {{ isset($bill) ? $bill->bill_id : '' }}</h2>
                        <p class="text-center">บิลนี้จะถูกเปลี่ยนสถานะเป็น ปิดบิล</p>
                        <div class="row justify-content-center">
                            <div class="col-12 col-md-10">
                                <div class="form-group row required">
                                    <div class="col-12">
                                        <label for="pay_cause">เหตุผลการยกเลิก</label>
                                        <select class="form-control disabled-w {{dForm($bill) !== null ? 'desc' : ''}}" name="bill_cause" id="cause-bill-void" >
                                            <option value="" selected>กรุณาเลือกเหตุผล</option>
                                            <option value="ไม่สามารถดำเนินการได้">ไม่สามารถดำเนินการได้</option>
                                            <option value="ลูกค้าประสงค์ยกเลิก">ลูกค้าประสงค์ยกเลิก</option>
                                            <option value="ออกบิลใหม่แทนบิลนี้">ออกบิลใหม่แทนบิลนี้</option>
                                            <option value="อื่นๆ">อื่นๆ</option>
                                        </select>
                                        <input type="text" class="d-none" name="id" value="{{ isset($bill) ? $bill->id : '' }}" readonly>
                                        <input type="text" name="user_id" class="d-none" value="{{ Auth::user()->id }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-center mb-0 text-danger">ตรวจสอบให้แน่ใจ ไม่สามารถกลับมาแก้ไขได้อีก</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-danger text-white btn-lg" id="btn-bill-void" role="button" disabled>ยืนยัน ยกเลิกยอดชำระ</button>
                    </div>
                </div>
            </div>
        </form>
    </div>


<div class="modal fade" id="modalPayError" tabindex="-1" role="dialog" aria-labelledby="modalPayError" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-err" role="document">
        <div class="modal-content">
            <div class="modal-body pt-4">
                <h2 class="text-center">บิลนี้ยังมียอดค้างชำระ</h2>
                <p class="text-center">กรุณาชำระเงินให้ครบก่อนปิดบิล</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modalCash" data-dismiss="modal">ชำระเงิน</button>
                <button type="button" class="btn btn-secondary btn-lg"  data-dismiss="modal">ยกเลิก</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalGoldError" tabindex="-1" role="dialog" aria-labelledby="modalGoldError" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-err" role="document">
            <div class="modal-content">
                <div class="modal-body pt-4">
                    <h2 class="text-center">ยังไม่ได้ลงยอดใช้ทอง</h2>
                    <p class="text-center">ถ้าหากไม่มีการใช้ทอง ให้เลือกบิลนี้ไม่ใช้ทอง</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modalGold" data-dismiss="modal">ลงทอง</button>
                    <button type="button" class="btn btn-secondary btn-lg"  data-dismiss="modal">ยกเลิก</button>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="modalGoldVoid" tabindex="-1" role="dialog" aria-labelledby="modalGoldError" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-err" role="document">
            <div class="modal-content">
                <div class="modal-body pt-4">
                    <h2 class="text-center">ต้องล้างยอดทอง</h2>
                    <p class="text-center">หากมีการยกเลิก การใช้ทองในรายการนี้</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary btn-lg" id="gold-void-cancle" data-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-danger btn-lg" id="gold-void" data-dismiss="modal">ยืนยัน ล้างยอดทอง</button>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="modalBillVoidError" tabindex="-1" role="dialog" aria-labelledby="modalBillVoidError" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-err" role="document">
            <div class="modal-content">
                <div class="modal-body pt-4">
                    <h2 class="text-center d-none d-md-block">แก้ไขยอดชำระ ก่อนยกเลิกบิล</h2>
                    <h3 class="text-center d-md-none">แก้ไขยอดชำระ ก่อนยกเลิกบิล</h3>
                    <p class="text-center">ยกเลิกประวัติการชำระให้ยอดเป็น 0.00<br>และคืนเงินลูกค้าให้ครบตามจำนวน</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modalPayHistory" data-dismiss="modal">ดูประวัติชำระ</button>
                    <button type="button" class="btn btn-secondary btn-lg"  data-dismiss="modal">ยกเลิก</button>
                </div>
            </div>
        </div>
    </div>

@if(Session::has('check-user'))
        <div class="modal fade" id="modalFlashUser" tabindex="-1" role="dialog" aria-labelledby="modalFlashUser" aria-hidden="true" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-flash-user" role="document">
                <div class="modal-content">
                    <div class="modal-body pt-4">
                        <h2 class="text-center">{{Auth::user()->u_name .' สาขา'. Auth::user()->branch->name}}</h2>
                        <p class="text-center m-0">ยินดีต้อนรับ {{Auth::user()->u_name}}</p>
                        <p class="text-center m-0">รายการจะถูกบันทึกลงใน สาขา{{Auth::user()->branch->name}}</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-primary btn-lg"data-dismiss="modal">เริ่มต้นใช้งาน</button>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" class="d-none" id="modalFlashUser-btn" data-toggle="modal" data-target="#modalFlashUser">
        </button>
        <script>
            $(document).ready(function($) {
                $(function () {
                    $("#modalFlashUser-btn").trigger('click');
                })

            });
        </script>
    @endif

@endsection

@section('scripts')
    <script>

        $(document).ready(function($) {

            setTimeout(function(){
                $('[autocomplete=off]').click();
                $("#name").prop("readonly", false);
            }, 100);

            $('#inputdatepicker').datepicker({
                autoclose: true,
                format: 'dd/mm/yyyy',
                todayBtn: 'linked',
                keyboardNavigation: false,
                language: 'th',
                thaiyear: true,
            })
            .attr("readonly","readonly");
                        
            $(function() {
                $('#inputdatepicker').val() === ''
                    ? $('#inputdatepicker').datepicker("setDate", "0")
                    : $('#inputdatepicker').datepicker("setDate", "{{isset($bill) ? $dateBc : '0'}}");
                $('#phone').val() !== '' ? $('#phone').trigger('keyup') : null;
                $('.text-area-job').trigger('change');
                $('.money-format').trigger('change');
                $('#service_cost').val(formatMoney(sumValue('.cost-job')));
                $('#total').val(formatMoney(sumValue('.cost-job,.material-job')));
                $('#material_cost').val(formatMoney(sumValue('.material-job')));
                $('.payment-method').trigger('change');
                $('#gold_input_check').trigger('change');
            });

            $('.input-group-text').click(function(){
                $('#inputdatepicker').data("datepicker").show();
            });

            $('#inputdatepicker').on('focus',function () {$(this).blur();})

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            $("#image-file").fileinput({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {'_token': "{{csrf_token()}}"},
                @if(isset($imagePart))
                @if(count($imagePart) > 0)
                    initialPreview: [
                        @foreach ($imagePart as $img)
                            "{{url('/').'/public/images/job/'. $img }}",
                        @endforeach
                    ],
                    initialPreviewConfig: [
                            @foreach ($imagePart as $i => $img)
                            {
                                url: "{{url('/bill/deleteImg')}}?delete={{$img}};{{$bill->id}}",
                                key: '{{$i}}',
                                extra: {id: 'init_{{$i}}'}
                            },
                            @endforeach
                    ],
                @endif
                @endif
                maxFileCount:4,
                initialPreviewAsData: true,
                overwriteInitial: false,
                validateInitialCount: true,
                theme: 'fa',
                showUpload:false,
                showClose:false,
                showCancel: true,
                dropZoneEnabled:false,
                uploadAsync: true,
                uploadUrl: "{{ url('bill/uploadimg') }}",
                uploadExtraData: function(previewId, index) {
                    return {
                        _token: "{{ csrf_token() }}",
                        key : index
                    };
                },
                allowedFileExtensions: ['jpg', 'png', 'jpeg'],
                maxImageHeight: 1000,
                resizeImage: true,
                maxFileSize: 30000,//30mb
            });

            $("#image-file").on("filepredelete", function(jqXHR,index) {
                $('.file-preview-frame[data-fileindex=init_'+index+"]").fadeOut(300);
            });

            $('#image-file').on('fileuploaderror', function(event, data, msg) {
                $(function() {
                $('.file-preview').css('border','1px solid #F44336');
                $('#gallery_error').text('กรุณาเพิ่มรูปงานซ่อม อย่างน้อย 1 รูป ไม่เกิน 4 รูป ');})

            });

            $("#image-file").on('change fileuploadsuccess',function () {
                $('.file-preview').css('border','1px solid #ddd')
                $('#gallery_error').text('');
            })

            $('#image_file_trigger').on('click',function () {
                $('#image-file').click()
            })

            // not-null-input-color

            $(".text-area-job, .cost-job").on("change", function(e) {
                e.preventDefault();
                let value = $(this).val();
                    if (value !== ''){
                        $(this).parent().addClass('not-null');
                        $(this).scrollTop(0);
                    } else {
                        $(this).parent().removeClass('not-null');
                    }
            });

            // reduce-money-format

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

            //auto_sum_cost

            $(".payment-method").on("change", function() {
                let value = sumValue('.payment-method,input[name="total_pay"]');
                let value_ = (sumValue('.cost-job') + sumValue('.material-job')) - value;
                $('#payment-method-total').text(formatMoney(value));
                $('#payment-remain').text( value_ >= 0 ? formatMoney( value_ ) : '0.00' );
                $('input[name="cash_val"]').val(value);
                $('input[name="cash"]').val(value).trigger('change');
                if(value_ >= 0){
                    $('#alert-payment').fadeOut(500);
                }

            });

            $('#payment-dissmiss').on('click',function () {
                $('.payment-method').val('').trigger('change');
            });

            $('#btn-add-payment').on('click',function () {
                let sum = sumValue('.payment-method,input[name="total_pay"]');
                let sumPay = sumValue('.payment-method');
                let total = sumValue('.cost-job') + sumValue('.material-job')
                let remain = total - sum;
                let totop = function () {
                    $(window).width() < 960 ? $('#modalCash').animate({
                        scrollTop: $('body').offset().top // Means Less header height
                    },400) : true ;
                }
                if(remain < 0){
                    $('#alert-payment').text('ยอดชำระเกินจำนวน').fadeIn(500).delay(2000).fadeOut(500)
                    $('.payment-method').val('').trigger('change')
                    totop();
                } else if (sumPay === 0){
                    $('#alert-payment').text('โปรดใส่จำนวนเงิน ก่อนเพิ่มรายการ').fadeIn(500).delay(2000).fadeOut(500)
                    $('.payment-method').val('')
                    totop();
                }
                else {
                    $('#modalCash-btn').click();
                }


            })

            $(".cost-job").on("change", function() {
                let value = formatMoney(sumValue('.cost-job'));
                $('#service_cost').val(value);
                $('#pay_service').text(value);
            });

            $('.material-job').on("change", function() {
                let value = formatMoney(sumValue('.material-job'));
                $('#material_cost').val(value);
                $('#pay_material').text(value)
            });

            $('.cost-job,.material-job').on("change", function() {
                let value = formatMoney(sumValue('.cost-job,.material-job'));
                $('input[name="cost_current"]').val(sumValue('.cost-job,.material-job'));
                $('#total').val(value);
                $('#pay_total,#payment-remain').text(value)
                $('#pay_total_ > h4 > strong').text(value + ' บาท')
            });

            // toggle-modal-member

            $('#modal-btn-new').on('click',function () {
                $('#customer_form').trigger("reset");
                $('.modal-no-member').fadeOut(100)
                $('.modal-add-member').delay(200).slideDown( "slow", function() {$(this).fadeIn(200);})
                $('#phone_').val($('#phone').val());
            })
            
            $('#modal-btn-notnow').on('click', function () {
                $('.modal-add-member').delay(200).slideUp( "slow", function() {$(this).fadeOut(200);})
            })

            $('#modal-btn-create').on('click',function (e) {
                let validCreat = validateCreateCustomer();
                if (validCreat) {
                    var url = "{{ url('customer/createWell') }}";
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: $("#customer_form").serialize(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(data, textStatus, xhr)
                        {
                            //console.log(xhr.status)
                            if (xhr.status === 200){
                                $('#phone').keyup();
                                $( "#myModal-btn" ).trigger( "click" );
                                $('.modal-add-member').delay(200).slideUp( "slow", function() {$(this).fadeOut(200);})
                                $(".alert-success").append($( ".alert-success span" )," เพิ่มข้อมูลลูกค้าเรียบร้อย");
                                $(".alert-success").fadeIn(500, function(){
                                    setTimeout(function () {
                                        $(".alert-success span").text("");
                                        $(".alert-success").fadeOut(500);
                                    },2000)
                                });

                            } else {
                                $(".alert-danger").append($( ".alert-danger span" )," ไม่สามารถเพิ่มข้อมูลลูกค้าได้");
                                $(".alert-danger").fadeIn(500, function(){
                                    setTimeout(function () {
                                        $(".alert-danger span").text("");
                                        $(".alert-danger").fadeOut(500);
                                    },2000)
                                });
                            }

                        },
                        statusCode: {
                            401: function() {
                                window.location.href = "{{ url('') }}"; //or what ever is your login URI
                            }
                        }
                    });

                    e.preventDefault();
                }
            })

            //auto-complete

            $('#phone').bind("keyup change",function(e) {
                let value = $(this).val();
                $('#search-name').hide()
                if(value.substring(0, 1) === '-'){
                    $("#name").val('').prop("readonly", true);
                    $('input[name="customer_id"]').val(1);
                    $('select[name="customer_type"]').val('สด1').prop("disabled", true);
                    $("#name").val('สด' || '-');
                    $('#search-name-clear').show()
                }
                else if (value.length > 10) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ url('customer/search') }}?phone="+ value,
                        dataType: "json",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function(){
                            // Show loading
                            $("#loader").show();
                        },
                        success: function (res) {
                            if(res.length > 0){
                                let data = res[0];
                                $("#name").val('').prop("readonly", true);
                                $('input[name="customer_id"]').val(data.id);
                                $('select[name="customer_type"]').val(data.customer_type).prop("disabled", true);
                                $("#name").val(data.name || '-');
                                $('#search-name-clear').show()
                            } else {
                                $('#search-name-clear').hide()
                                $("#name,#s_name,#company_name").val('').prop("readonly", false);
                                $('input[name="customer_id"]').val(0);
                                $('select[name="customer_type"]').val('สด1').prop("disabled", false);
                                // prevend on mobile phone number
                                let thMobile = ['06','08','09'].includes(value.substr(0, 2));
                                let keyNumber = !isNaN(Number(e.key)) || (e.keyCode === 229);
                                $('.modal-no-member').show();
                                if (!thMobile && value.length > 10 && keyNumber){
                                    $( "#myModal-btn" ).trigger( "click" );
                                } else if(value.length > 11 && keyNumber){
                                    $( "#myModal-btn" ).trigger( "click" );
                                }

                            }
                        },
                        complete:function(){
                            // Hide loading
                            $("#loader").hide();
                            // not found
                        },
                        statusCode: {
                            401: function() {
                                window.location.href = "{{ url('') }}"; //or what ever is your login URI
                            }
                        }
                    });

                 }
                else {
                    $("#name").val('').prop("readonly", false);
                    $('input[name="customer_id"]').val(0);
                    $('select[name="customer_type"]').val('สด1').prop("disabled", false);
                    $('#search-name-clear').hide()
                }
            });




            $('#name').bind("keyup change",function(e) {
                let value = $(this).val();
                let phone = $('#phone').val();
                console.log(phone)
                if (value !== '' && phone == ''){
                    $('#search-name-clear').hide()
                    $('#search-name').show()
                } else if(value !== '' && phone !== ''){
                    $('#search-name-clear').show()
                    $('#search-name').hide()
                }
                else {
                    $('#search-name').hide()
                }
                $('#drop-name-item').hide()
                $('#drop-name-notfound').hide()
            })

            $('#search-name').on("click",function(e) {
                let value = $('input[name="name"]').val();
                //console.log(value)
                if (value.length > 0) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ url('customer/searchWell') }}?name="+ value,
                        dataType: "json",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function(){
                            // Show loading
                            $("#loader-name").show();
                        },
                        success: function (res) {
                            console.log(res)
                            if(res.length > 0){
                                $('#drop-name-notfound').hide()
                                searchNameBox(res)
                                $("#drop-name-item").show()
                            } else {
                                $('#drop-name-notfound').show()
                                $("#drop-name-item").hide()
                            }
                        },
                        complete:function(){
                            // Hide loading
                            $("#loader-name").hide();
                            // not found
                        },
                        statusCode: {
                            401: function() {
                                window.location.href = "{{ url('') }}"; //or what ever is your login URI
                            }
                        }
                    });

                }
            });

            $('#search-name-clear').on('click',function () {
                $('input[name="name"]').val('').prop("readonly", false);
                $('input[name="phone"]').val('');
                $('input[name="customer_id"]').val(0);
                $('select[name="customer_type"]').val('สด1').prop("disabled", false);
                $('#search-name-clear').hide()
            })

            //on fucking submit

            $('#btn-submit').on('click',function () {

                let arrHasCostJob = [];
                let arrHasJob = [];
                let arrCostJob = [];
                let sumCost = 0;
                let arrJob = [];
                let arrMaterial = [];
                let selector = '.td-job.not-null';

                $(selector).each(function () {
                    let value = $(this).children().val();
                    let data = $(this).children().data();

                    if (value !== '' && data.hookCostJob !== undefined){
                        arrHasCostJob.push(data.hookCostJob)
                        let pre = {
                            job_id : data.hookCostJob,
                            value : parseFloat(value.replace(/,/g, ""))
                        };
                        arrCostJob.push(pre);
                        //console.log(arrCostJob)
                    } else if(value !== '' && data.hookJob !== undefined){
                        arrHasJob.push(data.hookJob)
                        let pre = {
                            job_id : data.hookJob,
                            amulet_id : data.hookAmulet,
                            amount : data.amount,
                            price : data.price,
                            value : value
                        };
                        arrJob.push(pre);

                        sumCost = sumCost + data.price;
                        if(sumCost === 0) {
                            arrHasCostJob.push(data.hookJob)
                        }

                    } else if(value !== '' && data.hookMaterial !== undefined){
                        let pre = {
                            material_id : data.hookMaterial,
                            value : parseFloat(value.replace(/,/g, ""))
                        };
                        arrMaterial.push(pre);
                    }


                })

                $('input[name="table_cost_job"]').val(JSON.stringify(arrCostJob));
                $('input[name="table_job"]').val(JSON.stringify(arrJob));
                $('input[name="table_material"]').val(JSON.stringify(arrMaterial));

                $(".form-check-input,#inputdatepicker").removeAttr("disabled");

                let hasJob = validateTable(arrHasCostJob,arrHasJob,selector,'hook-job');
                let hasCostJob = validateTable(arrHasJob,arrHasCostJob,selector,'hook-cost-job');
                let validTable = [hasJob,hasCostJob];
                let filesNum = $('#image-file').fileinput('getFilesCount');
                let filePreview = $('#image-file').fileinput('getPreview').content.length;
                let filesCount = filesNum - filePreview;


                if (validateInput(validTable)) {

                    if (filesCount > 0){
                        var submit = function(){
                            $('form#create_main').submit()
                        }
                        uploadImg(submit).done(function(){$("#myModal2-btn").trigger("click")});
                    }
                    else {
                        $('#btm-button').fadeOut("200")
                        $("#ajax_load").delay('200').fadeIn("200");
                        setTimeout(function () {
                            $('form#create_main').submit()
                        }, 2000);
                    }
                } else {
                    $( "#modalError-btn" ).trigger( "click" );
                }

            })

            $('.text-area-job').on('blur',function () {
                var value = $(this).val();
                if (value !== ''){
                    $(this).parent().parent().children().removeClass('error-td');
                }
                $(this).trigger('change');
            })

            //input job
            $('#allow_zero').on('click', function(){
                $('.text-area-job').trigger('change');
            })



            $('.text-area-job').on('change',function () {
                let value = $(this).val().split('/');
                let element = $(this).parent().children('.value-area-job');
                let checkFloat = $.isNumeric($(this).val().replace(/\//g, ''));
                let checkVal1 = value[1] < 0 || value[1] === '' ? false : true;
                let allow_zero = $('#allow_zero').is(":checked");
                let element_ = $(this);

                if (value[0] !== "" && value[0] > 0 && checkFloat && checkVal1) {
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
                        element.children('span.badge-amount').text('1' + ' ชิ้น')
                        element.children('span.badge-price').text('รวม ' + formatMoney(value[0]))
                        element_.attr("data-amount", 1);
                        element_.attr("data-price", value[0]);
                    }
                    element.show();
                    element_.hide();


                } else {

                    element.children('span.badge-amount').text('')
                    element.children('span.badge-price').text('')
                    element_.val('');
                    element_.attr("data-amount", '');
                    element_.attr("data-price", '');

                }
                sumRow(element_.data().hookJob, allow_zero);

            })


            $('.value-area-job').on('click',function (e) {
                if (e.currentTarget.className !== 'value-area-job confirm_job'){
                    let element_ = $(this).parent().children('.text-area-job');
                    e.preventDefault();
                    $(this).hide();
                    element_.show().focus();
                }
                else {
                    return false
                }

            })

            $('#customControlAutosizing').on('change',function () {
                if ($(this).get(0).checked){
                    $('#btn-conferm').prop('disabled',false)
                } else {
                    $('#btn-conferm').prop('disabled',true)
                }

            })

            $('.btn-void,.btn-void.oi').click(function (e) {
                let selector ='#row-' + e.target.id + ' td.name';
                console.log(e)
                var data = $(this).data();
                var str = $(selector).text();
                $( "span#name" ).html( str );
                $('input[name="t_id"]').val(data.id);

            });

            $('#pay_cause').on('keyup',function () {
                //console.log($(this).val())
                if ($(this).val() == ""){
                    $( "#btn-submit-void" ).prop('disabled',true)
                } else {
                    $( "#btn-submit-void" ).prop('disabled',false);
                }
            });

            //many-function

            $('#btn-print').on('click',function () {
                if ( !isUpdate() ){
                    let data = $(this).data().href;
                    window.location.href = data;
                } else {
                    $('#modalPrint-btn').trigger('click');
                }
            })

            $('#btn-modal-print').on('click',function () {
                $('#btn-submit').trigger('click');
            })

            //ฟัคยู ดีลีเวอร์
            $('#btn-modal-deliver').on('click',function () {
                let m = $('input[name="deliver_material"]').val();
                let gold_ = $('input[name="gold_"]').val();
                let gold_check = $('#gold_input_check').get(0).checked;
                if (isUpdate()){
                    $('#modalDeliverError-btn').trigger('click');
                }
                else {
                    $('#deliver-cost').text($('#total').val());
                    $('#deliver-material').text(m)
                    m !== '' ? $('#deliver-material_').show() : $('#deliver-material_').hide();
                    $('#modalDeliver-btn').trigger('click');
                }
            })

            $('#btn-modal-err-deliver').on('click',function () {
                $('#btn-submit').trigger('click');
            })
            
            //เด้ง modal ลงทอง

            $('#btn-close-success').on('click',function () {
                let oldCost = $('input[name="cost_data"]').val();
                let oldPay = $('input[name="cash_val"]').val();
                let gold_ = $('input[name="gold_"]').val();
                let gold_check = $('#gold_input_check').get(0).checked;
                if( gold_ == '' && !gold_check){
                    $('#modalGoldError-btn').click();
                    return false
                }else if( oldCost !== oldPay ){
                    $('#modalPayError-btn').click()
                    return false
                } else {
                    $('input[name="close_bill"]').val(1);
                    $('#btn-submit').click();
                }
            })

            $('#cause-bill-void').on('change',function () {
                let val = $(this).val()
                if (val !== ''){
                    $('#btn-bill-void').prop('disabled', false )
                } else {
                    $('#btn-bill-void').prop('disabled', true )
                }
            })
            
            $('#btn-modal-billvoid').on('click',function () {
                let oldPay = $('input[name="cash_val"]').val();
                console.log(oldPay)
                if(oldPay === '0'){
                    $('#modalBillVoid-btn').click()
                } else {
                    $('#modalBillVoidError-btn').click()
                }
            })

            //gold zone

            $('#gold').on('change',function () {
                let val = $(this).val();
                //console.log(val)
                if (val === '' && !isNaN(val)) {
                    $(this).val('');
                } else if(val.indexOf('.') == -1) {
                    $(this).val(parseFloat($(this).val()).toFixed(2));
                } else {
                    $(this).val(val);
                }
            });

            $('#gold_input_check').bind('click change',function () {
                let gold_ = $('input[name="gold_"]');
                if (!($(this).get(0).checked)){
                    $('#gold_input').show();
                } else {
                    if (gold_.val() > 0){
                        $('#modalGoldVoid-btn').click()
                    }
                    else {
                        $('#gold_input').hide();
                    }
                }
            })

            $('#gold-void').on('click',function () {
                let gold_ = $('input[name="gold_"], #craft_id_, input[name="gold_2"], #craft_id_2');
                $('#gold_input').hide();
                gold_.val('')
                $('#gold').trigger('change')
            })
            
            $('#gold-void-cancle').on('click',function () {
                $('#gold_input_check').prop('checked',false);
            })

            $('.sucess-element').bind('keyup change',function () {
                let gold_ = $('input[name="gold_"]').val() !== '' ?  $('input[name="gold_"]').val() : 0  ;
                let gold_c = $('#craft_id_').val();
                let gold_2 = $('input[name="gold_2"]').val() !== '' ?  $('input[name="gold_2"]').val() : 0 ;
                let gold_c2 = $('#craft_id_2').val();
                let gold_complete = gold_ !== 0 && gold_c !== null
                let gold_2_isactive = !$('#gold_second_toggle_').hasClass('d-none')
                let gold_2_complete = gold_2 !== 0 && gold_c2 !== null
                if (gold_2_isactive){
                    if(gold_complete && gold_2_complete){
                        $('#btn-gold').prop('disabled', false )
                    } else {
                        $('#btn-gold').prop('disabled', true )
                    }
                } else{
                    if(gold_complete){
                        $('#btn-gold').prop('disabled', false )
                    } else {
                        $('#btn-gold').prop('disabled', true )
                    }
                }
                $('#gold_value').val(formatMoney(parseFloat(gold_) + parseFloat(gold_2)))
            })

            $('#gold_second_toggle, #gold_second_toggle_').on('click', function () {
                $('#gold_second').toggleClass('d-none')
                $('#gold_second_toggle').toggleClass('d-none')
                $('#gold_second_toggle_').toggleClass('d-none')
                $('#craft_id_2, input[name="gold_2"]').val('')
                $('#gold').trigger('change')
            })

            $('#craft_id_2').on('focus',function () {
                let select = $('#craft_id_').find(":selected").val();
                $('#craft_id_2').children().each(function() {
                    if($(this).val() == select){
                        $(this).addClass('d-none')
                    } else {
                        $(this).removeClass('d-none')
                    }
                })
            })

            $('#craft_id_').on('focus',function () {
                let select = $('#craft_id_2').find(":selected").val();
                $('#craft_id_').children().each(function() {
                    if($(this).val() == select){
                        $(this).addClass('d-none')
                    } else {
                        $(this).removeClass('d-none')
                    }
                })
            })



            function sumValue(selector){
                let sum = 0;
                $(selector).each(function() {
                    var cost = $(this).val().replace(/,/g, "") || 0;
                    sum += parseFloat(cost);
                })
                return sum
            }

            function sumRow(rowId, allow_zero=false) {
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

                if(allow_zero)
                {

                    if (sum >= 0 && tr_element.length > 0) {
                        if(tr_element[0].classList.length < 3){
                            $('input[data-hook-cost-job='+rowId+']').val(formatMoney(sum));
                        } else {
                            $('input[data-hook-cost-job='+rowId+']').val("");
                        }
                    }
                    else {
                        $('input[data-hook-cost-job='+rowId+']').val("");
                    }
                }
                else
                {
                    if (sum > 0) {
                        $('input[data-hook-cost-job='+rowId+']').val(formatMoney(sum));
                    }
                    else {
                        $('input[data-hook-cost-job='+rowId+']').val("");
                    }
                }

                $(".cost-job").trigger('change');

            }

            function formatMoney(value){
                //console.log(value)
                return parseFloat(value, 10)
                    .toFixed(2)
                    .replace(/(\d)(?=(\d{3})+\.)/g, "$1,")
                    .toString()
            }

            function validateTable(arr_1, arr_2,items,data_) {
                console.log('arr_1',arr_1);
                console.log('arr_2',arr_2);
                let err = [];
                $.each(arr_1, function (index, val) {
                    if ($.inArray(val, arr_2) == -1) {
                        $('input[data-'+data_+'=' + val + ']').parent().addClass('error-td');
                        $('.text-area-job[data-'+data_+'=' + val + ']').parent().addClass('error-td');
                        err.push(1)
                    }
                });
                if(err.length !== 0 || arr_1.length === 0 || arr_2.length === 0 ){
                    $('#table_error').show();
                    $('.table-sort-amulet').css('border','1px solid #F44336')
                    return false
                } else {
                    $('#table_error').hide();
                    $('.table-sort-amulet').css('border','none')
                    return true
                }
            }

            function validateCreateCustomer() {
                let form = $('.customer-validation input.form-control').not($('input.not-require'));
                //console.log(form)
                let arrErr = [];
                //if ( extra.includes( false )){ arrErr.push(1) }
                form.each(function () {

                    let value = $(this).val();
                    if (value === ''){
                        $(this).addClass('is-invalid');
                        $(this).parent().children($('.invalid-feedback')).children().text('กรุณากรอกข้อมูล ');
                        arrErr.push(1)
                    }
                    else {
                        $(this).removeClass('is-invalid');
                        $(this).parent().children($('.invalid-feedback')).children().text('');
                    }
                })

                if( arrErr.length === 0 ) {
                    return true
                }

            }

            function validateInput(extra) {

                let form = $('.needs-validation input.form-control').not($('.needs-validation input.not-require'));
                let arrErr = [];
                let filesCount = $('#image-file').fileinput('getFilesCount');
                if ( filesCount > 4 ) {
                    arrErr.push(1)
                    $('.file-preview').css('border','1px solid #F44336')
                    $('#gallery_error').text('กรุณาเพิ่มรูปงานซ่อม อย่างน้อย 1 รูป ไม่เกิน 4 รูป ');
                };
                if ( extra.includes( false )){ arrErr.push(1) }
                form.each(function () {
                    let value = $(this).val();
                    if (value === ''){
                        $(this).addClass('is-invalid');
                        $(this).parent().children($('.invalid-feedback')).children().text('กรุณากรอกข้อมูล');
                        $('html, body').animate({
                            scrollTop: $('body').offset().top // Means Less header height
                        },400);
                        arrErr.push(1)
                    }
                    else {
                        $(this).removeClass('is-invalid');
                        $(this).parent().children($('.invalid-feedback')).children().text('');
                    }
                })

                if( arrErr.length === 0 ) {
                    return true
                }

            }

            function uploadImg(submit) {
                let selector = '#image-file';
                let filesNum = $(selector).fileinput('getFilesCount');
                let filePreview = $(selector).fileinput('getPreview').content.length;
                let filesCount = filesNum - filePreview;
                console.log(filesCount);
                //console.log($(selector).fileinput('getPreview'));
                if (filesCount > 0){
                    $(selector).fileinput('upload')
                    let arrImg = [];
                    $("#myModal2-btn").trigger("click");
                    $('html, body').animate({
                        scrollTop: $('body').offset().top// Means Less header height
                    },400);
                    setTimeout(function () {
                        $('#upload-bar')
                            .css('width', 7 + '%')
                            .attr('aria-valuenow', 7);
                    }, 200);
                    $(selector).on('fileuploaded', function (event, data, previewId, index) {
                        let response = data.response;
                        if (!(arrImg).includes(response) && arrImg.length !== filesCount + 1) {
                            arrImg.push(response);
                        }
                        let status = (arrImg.length / filesCount) * 99;
                        setTimeout(function () {
                            $('#upload-bar')
                                .css('width', status + '%')
                                .attr('aria-valuenow', status);
                        }, 500);
                        if (arrImg.length === filesCount) {
                            $('input[name="image_list"]').val(JSON.stringify(arrImg));
                            $(selector).on('filebatchuploadcomplete', function() {
                                setTimeout(function () {
                                    submit();
                                }, 2000);
                            });
                        }
                    });

                    $(selector).on('fileuploaderror', function(event, data, msg) {
                        arrImg = [];
                        if (data.jqXHR.status === 401){
                            window.location.href = "{{ url('') }}";
                        }
                        $('.file-preview').css('border','1px solid #F44336')
                        $("#myModal2-btn").trigger("click");
                        $('#gallery_error').text(data.jqXHR.status + ' ' + data.jqXHR.statusText);
                        $(this).fileinput('clear');
                        return false
                    });

                }
                else if(filePreview > 4){
                    $('.file-preview').css('border','1px solid #F44336')
                    $('#gallery_error').text('กรุณาเพิ่มรูปงานซ่อม อย่างน้อย 1 รูป ไม่เกิน 4 รูป ');
                }
            }
            
            function isUpdate() {
                let oldCost = $('input[name="cost_data"]').val();
                let currentCost = $('input[name="cost_current"]').val();
                let oldPay = $('input[name="total_pay"]').val();
                let currentPay = $('input[name="cash_val"]').val();
                let filesNum = $('#image-file').fileinput('getFilesCount');
                let filePreview = $('#image-file').fileinput('getPreview').content.length;
                let currentCustomer = $('input[name="customer_id"]').val();
                let oldCustomer = $('input[name="customer_old"]').val();
                let oldDate = $('input[name="old_date"]').val();
                let currentDate = $('input[name="date"]').val();
                let file = (filesNum - filePreview) === 0;
                let pay = (oldPay - currentPay) === 0;
                let cost = (oldCost - currentCost) === 0;
                let customer = (oldCustomer - currentCustomer) === 0;
                let date = (currentDate === oldDate);
                let gold_old = $('input[name="gold"]').val();
                let gold_2_old = $('input[name="gold2"]').val();
                let gold_ = $('input[name="gold_"]').val();
                let gold_2 = $('input[name="gold_2"]').val();
                let goldUpdate =  (gold_old == gold_) && (gold_2_old == gold_2)
                let gold_status_old = $('input[name="gold_status"]').val();
                let gold_check = $('#gold_input_check').get(0).checked ? 0 : 1;
                let gold_status = gold_status_old == gold_check ;
                console.log(gold_status_old);
                if (file && pay && cost && customer && date && goldUpdate && gold_status){
                    return false
                } else {
                    return true
                }
            }
            
            function searchNameBox(data) {

                $("#drop-name-item").html('').delay('200').append(
                    data.map(function (data) {
                        let elm =   '<div class="dropdown-item" ' +
                            'onclick="selectNameBox('+data.id+')" ' +
                            'data-n_id="'+data.id+'" ' +
                            'data-n_name="'+data.name+'" ' +
                            'data-n_customer_type="'+data.customer_type+'" ' +
                            'data-n_phone="'+data.phone+'" >' +
                            '<span class="drop-name-item">' +
                            '<h3>'+(data.name)+'</h3>' +
                            '<p>('+(data.customer_type)+')'+(data.phone)+'</p>' +
                            '</span>' +
                            '</div>'
                        return elm
                    })
                )
            }

        })

            function selectNameBox(id) {
            let data = $('div[data-n_id="'+id+'"]').data();
            $(function ($) {
                $('#phone').val(data.n_phone).keyup();
                $("#name").val('').prop("readonly", true).val(data.n_name || '-');;
                $('input[name="customer_id"]').val(data.n_id);
                $('select[name="customer_type"]').val(data.n_customer_type).prop("disabled", true);
                $('#search-name ,#drop-name-item,#drop-name-notfound').hide()
                $('#search-name-clear').show()
                $('#search-name').hide()
            })
        }

    </script>
@stop

