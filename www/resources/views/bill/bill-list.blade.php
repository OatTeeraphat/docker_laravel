@extends('layouts.app')

@section('content')
    @php
        $role = Auth::user()->roles[0]->level;
    @endphp

<div class="container">

    <div class="row justify-content-center">

        <div class="col-md-12">

            <div class="nav-pills-container mb-3">
                <ul class="nav nav-pills justify-content-center justify-content-md-start">
                    <li class="nav-item mr-2">
                        <a class="nav-link" href="{{url('bill')}}">บิลรับงาน</a>
                    </li>
                    <li class="nav-item mr-2">
                        <a class="nav-link active" href="{{url('recent')}}">ดูบิลเก่า</a>
                    </li>
                    <li class="nav-item mr-2">
                        <a class="nav-link" href="{{url('report')}}">รายงาน</a>
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

            $(window).scroll(function() {
                if($(window).scrollTop() + $(window).height() >= $(document).height()) {
                    @if($role < 4)
                        branch_id = 0;
                    @else
                        branch_id = $('select[name="branch_id"]').val();
                    @endif
                    loadMoreData(branch_id) ? $('#ajax_nomore').hide() : $('#ajax_load').hide();
                }
            });

            @if($role === 4)
            $('select[name="branch_id"]').on('change',function () {
                let branch_id = $('select[name="branch_id"]').val();
                paginateNum = 0;
                $("#feedContainer").html('')
                loadMoreData(branch_id);
            })
            @endif

            function loadMoreData(branch) {
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
                        $('#ajax_nomore').hide()
                    },
                    success : function(res){
                        if(res.length !== 0){
                            res.map(function (data,index) {
                                let index_ = ((slice+1) * (paginateNum)) + index;
                                return cardBox(data,index_);
                            });
                            paginateNum +=1;
                            $('#ajax_load').hide();
                            $('#ajax_nomore').show()
                        }
                    },
                    complete: function(){
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
                                            '<option value="">ทั้งหมด</option>'+
                                            '<option value="ยังไม่ปิดบิล">ยังไม่ปิดบิล</option>' +
                                            '<option value="ปิดบิลแล้ว">ปิดบิลแล้ว</option>' +
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
                                                '<th scope="col" class="disabled">ประเภท</th>' +
                                                '<th scope="col" class="disabled">ลูกค้า</th>' +
                                                '<th scope="col" class="disabled">โทรศัพท์</th>' +
                                                '<th scope="col" class="disabled">ส่งงาน</th>' +
                                                '<th scope="col" class="disabled">ชำระ</th>' +
                                                '<th scope="col" class="disabled">ปิดบิล</th>'+
                                                '<th scope="col" class="d-none">สถานะ</th>'+
                                            '</tr>'+
                                        '</thead>'+
                                            data.map(function (row) {
                                                //console.log(row)
                                                let elm = '<tr data-href="{{url("/")}}/bill/update?id='+row.bill_id+'" class="has-link">'+
                                                    '<td>'+row.bill_id+'</td>' +
                                                    '<td class="text-center">'+ (row.job_type === '1' ? 'งานซ่อม' : ( row.job_type === '2' ? 'แกะสลัก' : 'อื่นๆ' ) ) +'</td>' +
                                                    '<td>'+row.customer[0].name+'<small> ('+row.customer[0].customer_type+')</small></td>' +
                                                    '<td>'+(row.customer[0].phone !== null ? row.customer[0].phone : "-" )+'</td>' +
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
                            targets: [-1, -2, -3,-4],
                            width: "40px"
                        },
                        {
                            targets: [0,3],
                            width: "110px"
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
                    }
                });

                $('#table_select'+index).on('change',function () {
                    let value = $(this).val();
                    $('input[aria-controls=table'+ index +']').val(value).trigger('keyup');
                    $('#tableCount'+index).text(tab.$('tr', {"filter":"applied"}).length)
                })

                $('.dataTables_filter').parent().parent().hide();

                $('tr[data-href]').on("click", function() {
                    //console.log($(this).data('href'));
                    document.location = $(this).data('href');
                });

            }

        })

    </script>
@stop