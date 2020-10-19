@extends('layouts.app')

@section('content')
    @php
        function calDate( $item )
        {
            if (!is_null($item)){
                $dt = $item;
                $date = sprintf('%02d',$dt->day) .'/'.sprintf('%02d',$dt->month) .'/'.strval($dt->year + 543);
                return $date;
            }
        }
    @endphp
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
                    <div class="row">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb bg-transparent py-0">
                                <li class="breadcrumb-item active" aria-current="page">รายชื่อลูกค้า</li>
                            </ol>
                        </nav>
                    </div>
                <div class="card">
                    <div class="card-header"><h5>
                            <strong>รายชื่อลูกค้า</strong></h5></div>

                    <div class="card-body">

                        <div class="row">
                                <div class="col-12">
                                    <a class="btn btn-primary btn-lg" href="{{ url('customer/create') }}" role="button">เพิ่มลูกค้าใหม่</a>
                                    <button type="button" class="btn btn-outline-primary btn-lg ml-1" id="btn-data-print">พิมพ์</button>
                                    <button type="button" class="btn btn-outline-primary btn-lg" id="btn-data-csv">CSV</button>
                                    <hr/>
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <p>แสดงรายการ <span id="filter_rule">ทั้งหมด</span></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-3 mb-4 mb-md-0 form-group">
                                <input id="global_filter" class="form-control not-require column_filter" placeholder="ค้นหา : ทั้งหมด" data-name="ค้นหา" required>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3 mb-4 mb-md-0 form-group">
                                <input id="col1_filter" class="form-control not-require column_filter" data-column="1" placeholder="ค้นหา​ : ชื่อลูกค้า" data-name="ชื่อลูกค้า" required>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3 mb-3 mb-md-0 form-group">
                                <input id="phone"  class="form-control not-require column_filter" name="shit" autocomplete=false  placeholder="ค้นหา​ : โทรศัพท์" data-name="โทรศัพท์" required>
                            </div>
                        </div>
                        <div class="table-responsive table-customer">
                        <input type="password" class="d-none" />

                        <div id="Table_load" class="justify-content-center text-center">
                            <img src="{{ asset('public/img/loading-lg.gif') }}" alt="" style="width: 35px; padding: 20vh 0;text-align: center;"><span>กำลังดาวน์โหลด</span>
                        </div>
                        <table id="Table" class="table table-striped table-bordered table-report" style="display: none;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th width="20%">ชื่อ</th>
                                    <th width="15%">โทร</th>
                                    <th width="10%">ประเภท</th>
                                    <th width="20%">ที่อยู่</th>
                                    <th>Line</th>
                                    <th>สมัคร</th>
                                    <th>ใช้ล่าสุด</th>
                                    <th>ใหม่</th>
                                    <th scope="col" width="40">แก้ไข</th>
                                    <th scope="col" width="40">ลบ</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($listCustomer as $i => $item)
                                <tr id="row-{{ $i+1 }}">
                                    <td scope="row">{{ $item->id }}</td>
                                    <td class="name">{{ !is_null($item->name) ? $item->name : '-' }}</td>
                                    <td>{{ !is_null($item->phone) ? $item->phone : '-' }}</td>
                                    <td>{{ !is_null($item->customer_type) ? $item->customer_type : '-' }}</td>
                                    <td>{{ !is_null($item->address) ? $item->address : '-' }}</td>
                                    <td>{{ !is_null($item->line) ? $item->line : '-' }}</td>
                                    <td>{{ !is_null($item->created_at) ? calDate( $item->created_at ) : '-' }}</td>
                                    <td>{{ !is_null($item->updated_at) ? calDate( $item->updated_at ) : '-' }}</td>
                                    <td class="text-center"><span class="oi {{ $item->already_used  === 1 ? 'oi-circle-check' : 'oi-circle-x'}}"></span><span class="d-none">{{ $item->already_used}}</span></td>
                                    <td><a href="{{ url('customer/update?id='.$item->id) }}" class="badge badge-primary badge-icon"><span class="oi oi-pencil"></span></a></td>
                                    <td><a href="" data-id='{{ $item->id }}' data-path='{{ url('customer/delete?id=') }}' class="badge badge-danger badge-icon" data-toggle="modal" data-target="#exampleModal" id="{{ $i+1 }}"><span class="oi oi-trash " id="{{ $i+1 }}"></span></a></td>
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
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="d-none">
            <input type="password"/>
        </div>
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-body pt-4">
                    <h2 class="text-center">ต้องการลบลูกค้า <span id="name"></span></h2>
                    <p class="text-center">ลูกค้า<span id="name"></span>จะไม่ถูกแสดงในระบบ</p>
                </div>

                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">ยกเลิก</button>
                    <a class="btn btn-danger btn-lg"  role="button">ยืนยันลบ</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
        <script src="https://cdn.datatables.net/scroller/2.0.0/js/dataTables.scroller.min.js"></script>

        <script>

            $(document).ready(function($) {
                $('#Table').show()
                $('#Table_load').hide()
                $('#Table').DataTable({
                    responsive: true,

                    dom: 'Bifrt<"d-flex justify-content-between"lp>',
                    scrollY:        500,
                    scrollCollapse: true,
                    deferRender:    true,
                    scroller:       true,
                    columnDefs:[
                        {targets: [-1, -2],orderable: false},
                        {targets: [1, 2],width: '200px'},
                        {targets: [3],width: '40px'},
                        {targets: [4],width: '200px'}
                    ],
                    buttons:[
                        {
                            extend: 'print',
                            autoPrint: true,
                            footer: true,
                            text: 'พิมพ์',
                            title: 'รายชื่อลูกค้า',
                            exportOptions: {
                                columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ]
                            },
                            customize: function ( win ) {
                                $(win.document.body)
                                    .css( 'font-size', '11pt' )
                                $(win.document.body).find( 'thead' )
                                    .addClass( 'compact' )
                                    .css( 'font-size', 'inherit' );

                            },
                            messageTop: function () {
                                return reportHeader()
                            },
                            messageBottom: function () {
                            },

                        },
                        {
                            extend: 'csv',
                            title: 'รายชื่อลูกค้า',
                            charset: 'UTF-16LE',
                            bom: true,
                            exportOptions: {
                                columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ]
                            },
                        }

                    ],
                    language: {
                        zeroRecords: "ไม่พบรายการที่ต้องการ",
                        lengthMenu: "แสดง _MENU_ ต่อหน้า",
                        infoEmpty: "ไม่พบรายการที่ต้องการ",
                        infoFiltered: "(ค้นหาจาก _MAX_ รายการ)",
                        search: "ค้นหา :",
                        paginate: {
                            next: "ถัดไป",
                            previous: "ย้อนกลับ"
                        },
                        loadingRecords: "กรุณารอสักครู่...",
                        processing:     "กรุณารอสักครู่...",
                    },
                });

                $('a.badge.badge-danger,span.oi-trash').click(function (e) {
                    let selector ='#row-' + e.target.id + ' td.name';
                    var data = $(this).data();
                    var str = $(selector).text();
                    $( "span#name" ).html( str );
                    $( ".modal-footer a.btn-danger" ).attr( 'href', data.path+data.id );
                });

                $('#btn-data-print').on('click',function () {
                    $('.buttons-print').trigger('click')
                })

                $('#btn-data-csv').on('click',function () {
                    $('.buttons-csv').trigger('click')
                })

                $('#global_filter').on( 'keyup click', function () {
                    filterGlobal();
                } );

                $('input.column_filter').on( 'keyup click', function () {
                    filterColumn( $(this).attr('data-column') );
                } );

                $('#phone').bind( 'keyup keydown', function () {
                    filterPhone();
                } );

                $('select.column_filter').on( 'change', function () {
                    filterColumn( $(this).attr('data-column') );
                } );

                $('.column_filter').bind('keyup change',function () {
                    var arrText= $('.column_filter').map(function(){
                        if(this.value !== ''){
                            let text = this.value
                            let att = $(this).data('name');
                            return att +' '+text;
                        }
                    }).get().join(", ");
                    if (arrText !== ''){
                        $('span#filter_rule').text(arrText);
                    } else {
                        $('span#filter_rule').text('ทั้งหมด');
                    }
                })

                $('tr[data-href]').on("click", function() {
                    let bill_id = $(this).children('.bill-id').text();
                    //console.log(bill_id);
                    document.location = $(this).data('href')+bill_id;
                });


                function filterGlobal () {
                    $('#Table').DataTable().search(
                        $('#global_filter').val()
                    ).draw();
                }

                function filterColumn ( i ) {
                    let val = $('#col'+i+'_filter').val();
                    //console.log(val)
                    $('#Table').DataTable().column( i ).search(
                        val
                    ).draw();
                    if (val !== ''){
                        $('#col'+i+'_filter').removeClass('is-null')
                    } else {
                        $('#col'+i+'_filter').addClass('is-null')
                    }
                }

                function filterPhone ( ) {
                    let val = $('#phone').val();
                    //console.log(val)
                    $('#Table').DataTable().column( 2 ).search(
                        val
                    ).draw();
                    if (val !== ''){
                        $('#phone').removeClass('is-null')
                    } else {
                        $('#phone').addClass('is-null')
                    }
                }

                function reportHeader() {
                    let rule = $('span#filter_rule').text()
                    let info = $('#Table_info').text();
                    let count_list = info.indexOf("(") == -1 ? '0' : parseInt(info.split('(')[1]);

                    let header =
                        '<div class="dt-print-header d-flex align-item-center">' +
                        '<img src="{{ url('/') }}/public/images/jewerly-t.png" />'+
                        '<div class="dt-haed-sub align-self-center">'+
                        '<h3>'+'รายชื่อลูกค้า'+'</h3>' +
                        '<p>รายการ : '+ rule +'</p>' +
                        '</div>'+
                        '<div class="dt-haed-sub align-self-center ml-auto">'+
                        '<p>จำนวน : '+ count_list + ' รายการ</p>'+
                        '</div>'+
                        '</div>';

                    return header
                }

            } );

        </script>
@stop



