@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="row">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent py-0">
                            <li class="breadcrumb-item"><a href="{{url('customer')}}">รายชื่อลูกค้า</a></li>
                            <li class="breadcrumb-item active" aria-current="page">แก้ไขข้มูลลูกค้า</li>
                        </ol>
                    </nav>
                </div>
                <div class="card">
                    <div class="card-header"><h5><strong>แก้ไขข้มูลลูกค้า</strong></h5></div>
                    <div class="card-body">
                        <form method="POST" action="{{ url('customer/update') }}">
                            @csrf
                            <input type="hidden" name="id" value="{{ $customer->id }}">
                            <div class="form-group row required">
                                <label for="customer_type" class="col-md-4 col-form-label text-md-right">ประเภทลูกค้า</label>

                                <div class="col-md-6">
                                    <select id="customer_type" class="form-control" name="customer_type" required>
                                        <option value="สด1" {{ 'สด1'  == $customer->customer_type ? 'selected' : '' }} >สด 1</option>
                                        <option value="สด2" {{ 'สด2'  == $customer->customer_type ? 'selected' : '' }} >สด 2</option>
                                        <option value="สด3" {{ 'สด3'  == $customer->customer_type ? 'selected' : '' }} >สด 3</option>
                                    </select>
                                    @if ($errors->has('branch_id'))
                                        <span class="invalid-feedback">
                                        <strong>กรุณาระบุประเภทลูกค้า</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row required">
                                <label for="name" class="col-md-4 col-form-label text-md-right">ชื่อ</label>
                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ $customer->name }}" required autofocus>

                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row" id="address_group">
                                <label for="address" class="col-md-4 col-form-label text-md-right">ที่อยู่</label>
                                <div class="col-md-6">
                                    <textarea id="address" type="text" class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}" name="address" autofocus rows="3">{{ $customer->address }}</textarea>

                                    @if ($errors->has('address'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row required">
                                <label for="phone" class="col-md-4 col-form-label text-md-right">หมายเลขโทรศัพท์</label>
                                <div class="col-md-6">
                                    <input id="phone" type="text" maxlength="12" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ $customer->phone }}" autofocus>
                                    <span class="invalid-feedback">
                                        <strong id="phone_error">@if ($errors->has('phone')){{ $errors->first('phone')}}@endif</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="line" class="col-md-4 col-form-label text-md-right">Line ID</label>
                                <div class="col-md-6">
                                    <input id="line" type="text" class="form-control{{ $errors->has('line') ? ' is-invalid' : '' }}" name="line" value="{{ $customer->line }}" autofocus>

                                    @if ($errors->has('line'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('line') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        {{ __('ยืนยันการแก้ไข') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function($) {

            $('#customer_type').on('change', function () {

                var optionSelected = $(this).find("option:selected")
                var valueSelected  = optionSelected.val()

                switch (valueSelected){
                    case 'company' :
                        $("label[for='nick_name']").text('ชื่อผู้ติดต่อ')
                        $("label[for='name']").text('ชื่อบริษัท')
                        $('#address_group').addClass('required')
                        $("#s_name_group").hide()
                        $("#tax_number_group").show()

                        break;
                    case 'customer' :
                        $("label[for='name']").text('ชื่อ')
                        $("label[for='nick_name']").text('ชื่อเล่น')
                        $('#address_group').removeClass('required')
                        $("#s_name_group").show()
                        $("#tax_number_group").hide()
                        break;
                }
            });
            $('#customer_type').trigger('change');
        } );
    </script>
@stop


