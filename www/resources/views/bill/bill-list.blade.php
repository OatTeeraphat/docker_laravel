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

            @if($role === 4)
            <div class="card mb-2 mb-md-3">
                <div class="card-body">
                    <div class="form-group row required mb-0 justify-content-between">
                        <div class="col-12 col-md-4 ">
                            <select id="branch_id" class="custom-select custom-select-md" name="branch_id" required>
                                <option value="0" selected>แสดงทุกสาขา</option>
                                @foreach ($branch as $data)
                                    <option value="{{ $data->id }}" >{{ $data->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            @endif


            <div class="col-12 col-md-11 mb-3 report-container">
                <div class="table-report table-responsive">
                    <table id="table" class="table table-hover table-bordered table-report" style="width:100%">
                        <thead>
                        <tr>
                            <th class="disabled">#</th>
                            @foreach($report_header as $th)
                                <th class="disabled">{{$th->name}}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $i => $d)
                            <tr {{ $link ? 'data-href='. url("/") .'/bill/update?id='.$d->bill_id : '' }}>
                                <td>{{$i+1}}</td>
                                @foreach($report_header as $th)
                                    @php($param = $th->field)
                                    <td {{$param == 'bill_id' ? 'class=bill-id' : ''}}>{{$d->$param}}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="feedContainer"></div>
            <div id="ajax_load" class="row justify-content-center">
                <img src="{{url('/')}}/public/img/loading-lg.gif" alt="">
            </div>
            <div id="ajax_nomore" class="row justify-content-center mt-5 my-3" >
                <h3 class="text-muted">ไม่มีบิลที่จะแสดงแล้ว</h3>
            </div>

        </div>

    </div>

</div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function($) {

            let paginateNum = 0;
            let slice = 3;
            let branch_id;

            $(function () {
                $(window).trigger('scroll')
            });

            $(window).on('scroll', function (e) {
                if($(window).scrollTop() + window.innerHeight >= $(document).height()) {
                    @if(!$role < 4)
                        branch_id = 0;
                    @else
                        $('#ajax_load').show();
                        branch_id = $('select[name="branch_id"]').val();
                    @endif
                    loadMoreData(branch_id) ? $('#ajax_nomore').hide() : $('#ajax_load').hide();
                }
            })

            @if($role === 4)
            $('select[name="branch_id"]').on('change',function () {
                let branch_id = $('select[name="branch_id"]').val();
                paginateNum = 0;
                $("#feedContainer").html('')
                loadMoreData(branch_id);
            })
            @endif

            function loadMoreData(branch) {
                $('#ajax_load').show();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var jax = $.ajax({
                    type : 'GET',
                    @if($role < 4)
                    url  : "{{url('/')}}/api/bill?ref=" + paginateNum + "!" + slice + "!{{ Auth::user()->branch_id }}",
                    @else
                    url  : "{{url('/')}}/api/bill?ref=" + paginateNum + "!" + slice + "!" + branch,
                    @endif
                    dataType: 'JSON',
                    beforeSend: function()
                    {
                        $('#ajax_load').show();
                    },
                    success : function(res){
                        if(res.length !== 0){
                            res.map(function (data,index) {
                                let index_ = ((slice+1) * (paginateNum)) + index;
                                return cardBox(data,index_);
                            });
                            paginateNum +=1;

                        }
                    },
                    complete: function(){
                        $('#ajax_nomore').show()
                        return false;
                    },
                    error: function(){
                        return false;
                    },
                    statusCode: {
                        401: function() {
                            window.location.href = "{{ url('') }}"; //go login URI
                        }
                    }

                })

                if (jax !== null){
                    $('#ajax_nomore').show()
                }

            }

            function cardBox(data,index) {

               let element = '<div class="card mb-2 mb-md-3">'+
                    '<div class="card-header">'+
                        '<h5><strong>บิลวันที '+ data[0].date +'</strong></h5>'+
                    '</div>'+
                    '<div class="card-body pt-4">' +
                        '<div class="row justify-content-center">' +
                            '<div class="col-md-10">' +
                                '<div class="row">' +
                                    '<div class="col-md-3 col-6 mb-2">' +
                                        '<select class="custom-select" id="table_select'+index+'">' +
                                            '<option value="ยังไม่ปิดบิล" selected>ยังไม่ปิดบิล</option>' +
                                            '<option value="ปิดบิลแล้ว">ปิดบิลแล้ว</option>' +
                                            '<option value="">ทั้งหมด</option>'+
                                        '</select>' +
                                    '</div>'+
                                    '<div class="col-md-9 col-6 mt-2 d-none d-md-block">' +
                                        '<p class="float-right mb-1">จำนวน : <span id="tableCount'+index+'">'+data.length+'</span> รายการ</p>'+
                                    '</div>'+
                                '</div>'+
                                '<div class="table-responsive mb-3">'+
                                    '<table id="table'+ index +'" class="table table-hover table-bordered table-bill">'+
                                        '<thead>'+
                                            '<tr>'+
                                                '<th scope="col" class="disabled">เลขที่บิล</th>' +
                                                '<th scope="col" class="disabled">รูป</th>' +
                                                '<th scope="col" class="disabled">ประเภท</th>' +
                                                '<th scope="col" class="disabled">ลูกค้า</th>' +
                                                '<th scope="col" class="disabled">ยอดรวม</th>' +
                                                '<th scope="col" class="disabled">ใช้ทอง</th>' +
                                                '<th scope="col" class="disabled">ส่งงาน</th>' +
                                                '<th scope="col" class="disabled">ชำระแล้ว</th>' +
                                                '<th scope="col" class="disabled">ปิดบิล</th>'+
                                                '<th scope="col" class="d-none">สถานะ</th>'+
                                            '</tr>'+
                                        '</thead>'+
                                            data.map(function (row) {
                                                console.log(row)
                                                let img_part = row.image_part !== null
                                                        ? ((row.image_part).split(",")[0]) !== '' ?
                                                                '{{url("/") }}/public/images/job/' +(row.image_part).split(",")[0]
                                                            :   '{{url("/") }}/public/images/job/' +(row.image_part).split(",")[1]
                                                        :  '{{url("/") }}/public/img/' + 'noimg.png'
                                                //console.log(img_part)
                                                let elm = '<tr data-href="{{url("/")}}/bill/update?id='+row.bill_id+'" class="has-link">'+
                                                    '<td class="bill-id">'+row.bill_id+'</td>' +
                                                    '<td class="bill-id"><div class="img-fit" style="background: url('+ img_part +')"></div></td>' +
                                                    '<td class="text-center">'+ (row.job_type === '1' ? 'งานซ่อม' : ( row.job_type === '2' ? 'แกะสลัก' : 'อื่นๆ' ) ) +'</td>' +
                                                    '<td>'+row.customer[0].name+'<small> ('+row.customer[0].customer_type+')</small></td>' +
                                                    '<td class="money-format">'+(sumCost(row.order) + sumCost(row.part)) +'</td>' +
                                                    '<td>'+(sumGold(row.gold))+'</td>' +
                                                    '<td class="text-center" >'+(
                                                        row.deliver  === 1 ? '<span class="oi oi-circle-check"></span>'
                                                            : '<span class="oi oi-circle-x"></span>'
                                                    )+'<span class="d-none">'+row.deliver+'</span></td>' +
                                                    '<td class="text-center" >'+(
                                                        row.pay  === 1 ? '<span class="oi oi-circle-check"></span>' :
                                                            '<span class="oi oi-circle-x"></span>'
                                                    )+'<span class="d-none">'+row.pay+'</span></td>' +
                                                    '<td class="text-center" >'+(
                                                        row.status === 1 ? '<span class="oi oi-circle-check"></span>'
                                                            : (row.status  === 0 ? '<span class="oi oi-circle-x"></span>'
                                                            :'<span class="badge badge-secondary">ยกเลิก</span>')
                                                    )+'<span class="d-none">'+row.status+'</span></td>' +
                                                    '<td class="d-none">'+((row.status === 1 || row.status === 2) ? 'ปิดบิลแล้ว' : 'ยังไม่ปิดบิล')+'</td>' +
                                                    '</tr>';
                                                return elm
                                                }) +
                                        '<tbody>'+
                                        '</tbody>'+
                                    '</table>'+
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';

                let strNewString = element.replace(/,/g,'');
                $("#feedContainer").append(strNewString)

                let tab = $('#table' + index).DataTable({
                    responsive: true,
                    order: [[ 0, "desc" ]],
                    columnDefs:[
                        {
                            targets: [6,7,8],
                            width: "20px"
                        },
                        {
                            targets: [3],
                            width: "110px"
                        },
                        {
                            targets: [0],
                            width: "135px"
                        },
                        {
                            targets: [1],
                            width: "50px"
                        },
                    ],
                    paging : false,
                    bInfo: false,
                    language: {
                        zeroRecords: "ไม่พบบิลที่ต้องการ จากทั้งหมด " +data.length+ " รายการ"
                    },
                    oSearch: {sSearch: "ยังไม่ปิดบิล" },
                    fnDrawCallback: function () {

                        $('tr[data-href]').click(function () {
                            var href = $(this).data('href')
                            document.location.href = href
                        })

                    }
                });


                $('#table_select'+index).on('change',function () {
                    let value = $(this).val();
                    $('input[aria-controls=table'+ index +']').val(value).trigger('keyup');
                    $('#tableCount'+index).text(tab.$('tr', {"filter":"applied"}).length)
                })

                $('#table_select'+index).trigger('change');

                $('.dataTables_filter').parent().parent().hide();

                $('tr[data-href]').on("click","tbody tr", function() {
                    let bill_id = $(this).data();
                    console.log(bill_id);
                    //document.location = $(this).data('href');
                });

            }


            function sumCost(data){
                let cost = 0
                if (data.length > 0) {
                    data.map(function (row) {
                        cost += row.price
                    })
                }
                return cost
            }

            function sumGold(data){
                let cost = 0
                if (data.length > 0) {
                    data.map(function (row) {
                        cost += row.value
                    })
                }
                return formatMoney(cost)
            }

            function formatMoney(value){
                //console.log(value)
                return parseFloat(value, 10)
                    .toFixed(2)
                    .replace(/(\d)(?=(\d{3})+\.)/g, "$1,")
                    .toString()
            }



        })

    </script>
@stop