@extends('layouts.app')

@section('content')

    @php
    //var_dump($data);
    $role = Auth::user()->roles[0]->level;
    @endphp

    <div class="container">
        <div class="row justify-content-center mb-3">
            <div class="col-md-12">

                <div class="row">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent py-0 not-print">
                            <li class="breadcrumb-item active" aria-current="page">ออกรายงาน</li>
                        </ol>
                    </nav>
                </div>

                <form method="POST" action="{{ url('report') }}" autocomplete="off">
                    @csrf
                    <div class="card mb-2 mb-md-3">
                        <div class="card-body">
                            <div class="form-group row required mb-0 justify-content-start">
                                <div class="col-12 col-md-4">
                                    <select id="branch_id" class="custom-select custom-select-md" name="branch_id" required>
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
                            </div>
                        </div>
                    </div>
                    <div class="card mb-2 mb-md-3">
                        <div class="card-body card-report">
                                <div class="row">
                                <div class="col-lg-4 col-md-12 col-12 form-group">
                                    <label for="report_id" class="report-label">รายงาน</label>
                                    <select id="report_id" class="custom-select custom-select-md" name="report_id" required>
                                        @foreach( $report as $r)
                                            <option value="{{$r->id}}" {{ !isset($current) ?: ($current->report_id == $r->id ? 'selected' : '') }}>{{$r->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12 form-group">
                                    <label for="inputdatepicker" class="report-label">ตั้งแต่วันที่</label>
                                    <div class="input-group">
                                        <input id="inputdatepicker" name="date_start"  class="datepicker datepicker1 form-control not-require" required value="{{isset($current) ? $current->start : ''}}">
                                        <div class="input-group-append input-append-button">
                                            <span class="input-group-text"><span class="oi oi-calendar"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12 mb-4 mb-md-0 form-group">
                                    <label for="inputdatepicker2" class="report-label">จนถึงวันที่</label>
                                    <div class="input-group">
                                        <input id="inputdatepicker2" name="date_end"  class="datepicker datepicker2 form-control not-require" required value="{{isset($current) ? $current->end : ''}}">
                                        <div class="input-group-append input-append-button">
                                            <span class="input-group-text input-group-text2"><span class="oi oi-calendar"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-6 col-12 mb-0 form-group">
                                    <label for="" class="report-label d-none d-lg-inline-block"></label>
                                    <div class="input-group">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block btn-report-search">ค้นหา</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <strong>
                                {{ isset($current) ? $current->report : 'ยอดรวมงานซ่อม'}}
                            </strong><br class="d-md-none">
                            <small class="float-md-right mt-md-1">{{ isset($current) ? $current->report_desc : $report_desc}}</small>
                        </h4>

                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-12 col-md-11 mt-2 report-container">
                                <button type="button" class="btn btn-primary btn-lg" id="btn-data-print">พิมพ์</button>
                                <button type="button" class="btn btn-primary btn-lg" id="btn-data-csv">CSV</button>
                                <hr class="mb-0">
                            </div>
                            <div class="col-12 col-md-11 mt-3 report-container">
                                <h2 class="mb-0">ยอดรวม : <span class="text-success"><span id="sum" class="money-format">0.00</span>{{$unit}}</span></h2>
                                <p>แสดงรายการ <span id="filter_rule">ทั้งหมด</span></p>
                                <div class="row mt-3">
                                    <div class="col-12 col-md-6 col-lg-3 mb-4 mb-md-3 form-group">
                                        <input id="global_filter" class="form-control not-require column_filter" placeholder="ค้นหา : ทั้งหมด" data-name="ที่มีคำว่า" required value="">
                                    </div>

                                    @foreach($report_header as $i => $search)
                                        @if($search->search)
                                            @if(isset($search->select) && $search->search)
                                                <div class="col-12 col-md-6 col-lg-3 mb-4 mb-md-3 form-group">
                                                    <select id="col{{$i+2}}_filter" class="custom-select custom-select-md column_filter is-null" data-column="{{$i+2}}" data-name="{{$search->name}}">
                                                        <option class="text-muted" value="">ค้นหา​ : {{$search->name}}</option>
                                                        @foreach( $search->select as $r)
                                                            <option value="{{$r->name}}">{{$r->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @else
                                                <div class="col-12 col-md-6 col-lg-3 mb-4 mb-md-3 form-group">
                                                    <input id="col{{$i+2}}_filter" class="form-control not-require column_filter" data-column="{{$i+2}}" placeholder="ค้นหา​ : {{$search->name}}" data-name="{{$search->name}}" required>
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-12 col-md-11 mb-3 report-container">
                                <div class="table-report table-responsive">
                                    <table id="table" class="table table-hover table-bordered table-report" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th class="disabled"></th>
                                            <th class="disabled">#</th>
                                            @foreach($report_header as $th)
                                                <th class="disabled">{{$th->name}}</th>
                                            @endforeach
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($data as $i => $d)
                                        <tr {{ $link ? 'data-href='. url("/") .'/bill/update?id='.$d->bill_id : '' }}>
                                            <td>
                                                <a href="{{$link ? (url("/") .'/bill/update?id='.$d->bill_id) : ''}}" class="badge badge-primary badge-icon not-pointer">
                                                    <span class="oi oi-pencil"></span>
                                                </a>
                                            </td>
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
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </div>


@endsection

@section('scripts')

    <script src="{{ asset('public/js/report.js') }}" defer></script>
    <script>
        $(document).ready(function($) {

            $('#table').DataTable({
                dom: 'Bifrt<"d-flex justify-content-between "lp>',
                buttons: [
                    {
                        extend: 'print',
                        autoPrint: true,
                        footer: true,
                        text: 'พิมพ์',
                        title: '{{ isset($current) ? $current->report : 'ยอดรวมงานซ่อม'}} {{ isset($current) ? $current->report_desc : $report_desc}}',
                        exportOptions: {
                            columns: ':visible'
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
                        title: '{{ isset($current) ? $current->report : 'ยอดรวมงานซ่อม'}} {{ isset($current) ? $current->report_desc : $report_desc}}',
                        charset: 'UTF-16LE',
                        bom: true
                    }

                ],
                order: [[ 1, "asc" ]],
                columnDefs:[
                    {
                        targets: [1],
                        width: "20px"
                    },
                    {
                        targets: [-1],
                        className: "dt-right"
                    },
                    {
                        targets: [0],
                        orderable: false
                    }
                ],
                bInfo: true,
                language: {
                    zeroRecords: "ไม่พบรายการที่ต้องการ",
                    lengthMenu: "แสดง _MENU_ ต่อหน้า",
                    info: "หน้า _PAGE_ จาก _PAGES_ หน้า (_TOTAL_ รายการ)",
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
                footerCallback: function ( row, data, start, end, display ) {
                    var api = this.api(), data;
                    let intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };
                    let total = api
                        .column( -1 ,{ search: 'applied'})
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
                    $('#sum').text(formatMoney(total));
                }
            });

            $(function() {
                $('#inputdatepicker').val() === ''
                    ? $('#inputdatepicker').datepicker("setDate", "0")
                    : $('#inputdatepicker').datepicker("setDate", "{{isset($current) ? $current->start : ''}}");
                $('#inputdatepicker2').val() === ''
                    ? $('#inputdatepicker2').datepicker("setDate", "0")
                    : $('#inputdatepicker2').datepicker("setDate", "{{isset($current) ? $current->end : ''}}");
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
                document.location = $(this).data('href');
            });

            function filterGlobal () {
                $('#table').DataTable().search(
                    $('#global_filter').val()
                ).draw();
            }

            function filterColumn ( i ) {
                let val = $('#col'+i+'_filter').val();
                //console.log(val)
                $('#table').DataTable().column( i ).search(
                    val
                ).draw();
                if (val !== ''){
                    $('#col'+i+'_filter').removeClass('is-null')
                } else {
                    $('#col'+i+'_filter').addClass('is-null')
                }
            }
            
            function reportHeader() {
                let rule = $('span#filter_rule').text()
                let sum = $('#sum').text();
                let date1 = '{{isset($current) ? $current->date_start : $report_date}}';
                let date2 = '{{isset($current) ? $current->date_end : ''}}';
                let info = $('#table_info').text();
                let count_list = info.indexOf("(") == -1 ? '0' : parseInt(info.split('(')[1]);

                let header1 =
                    '<div class="dt-print-header d-flex align-item-center">' +
                    '<img src="{{ url('/') }}/public/images/jewerly-t.png" />'+
                    '<div class="dt-haed-sub align-self-center">'+
                    '<h3>'+'{{ isset($current) ? $current->report : 'ยอดรวมงานซ่อม'}}'+'</h3>' +
                    '<p>สาขา : '+'{{ isset($current) ? $current->branch_name : $report_branch }}'+'</p>' +
                    '<p>รายการ : '+ rule +'</p>' +
                    '<p>ยอดรวม : '+ sum  +' {{$unit}}</p>' +
                    '</div>'+
                    '<div class="dt-haed-sub align-self-center ml-auto">'+
                    '<p>ตั้งแต่ : '+ '{{isset($current) ? $current->date_start : $report_date}}' + '</p>'+
                    '<p>จนถึง : '+ '{{isset($current) ? $current->date_end : ''}}' + '</p>'+
                    '<p>จำนวน : '+ count_list + ' รายการ</p>'+
                    '</div>'+
                    '</div>';
                let header2 =
                    '<div class="dt-print-header d-flex align-item-center">' +
                    '<img src="{{ url('/') }}/public/images/jewerly-t.png" />'+
                    '<div class="dt-haed-sub align-self-center">'+
                    '<h3>'+'{{ isset($current) ? $current->report : 'ยอดรวมงานซ่อม'}}'+'</h3>' +
                    '<p>สาขา : '+'{{ isset($current) ? $current->branch_name : $report_branch}}'+'</p>' +
                    '<p>รายการ : '+ rule +'</p>' +
                    '<p>ยอดรวม : '+ sum  +' {{$unit}}</p>' +
                    '</div>'+
                    '<div class="dt-haed-sub align-self-center ml-auto">'+
                    '<p>วันที่ : '+ '{{isset($current) ? $current->start : $report_date}}' + '</p>'+
                    '<p>จำนวน : '+ count_list + ' รายการ</p>'+
                    '</div>'+
                    '</div>';

                    if (date1 !== date2 && date2 !== ''){
                        return header1
                    } else {
                        return header2
                    }


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