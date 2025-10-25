@extends('layouts.gen')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('reports') }}">Reports </a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-12 col-md-12 col-sm-12 text-right pt-0">
               <form class="row g-3 dashform" action="{{url('expenses-report')}}" method="POST">
                    @csrf
                    <div class="col-md-3">
                        <select name="store" class="form-select form-select-sm mb-1" onchange='this.form.submit();'>
                            @if (!is_null($currstore))
                                <option value="{{ $currstore->id }}">{{ $currstore->name }}</option>
                            @endif
                            <option value="">All Stores</option>
                            @foreach ($shops as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="expense_category_id" onchange="this.form.submit()" class="form-select form-select-sm mb-1">
                            <option value="">---Select Expense Category ---</option>
                            @foreach($expcategories as $cat)
                            @if(!is_null($expcat) && $expcat->id == $cat['id'])
                            <option value="{{$cat['id']}}" selected>{{$cat['name']}}</option>
                            @else
                            <option value="{{$cat['id']}}">{{$cat['name']}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                    <!-- Date and time range -->
                    <div class="col-md-6">
                        <div class="input-group">
                            <button type="button" class="btn btn-sm btn-white btn-sm pull-right" id="reportrange">
                            <span><i class="fa fa-calendar"></i></span>
                                <i class="fa fa-caret-down"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2 nav-success" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#total-expenses-pdf" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-minus font-18 me-1'></i></div>
                                    <div class="tab-title">{{trans('navmenu.total_expense_report')}} (PDF)</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#total-expenses" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-minus font-18 me-1'></i></div>
                                    <div class="tab-title">{{trans('navmenu.total_expense_report')}} (Excel)</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#expenses-report" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-plus font-18 me-1'></i></div>
                                    <div class="tab-title">{{trans('navmenu.expense_report')}}</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="total-expenses-pdf" role="tabpanel">
                            <div class="row g-1 print_invoice" id="print-exp-report">
                                <div class="col-md-12">
                                    <table class="table mb-1">
                                        <tbody>
                                            <tr>
                                                <td colspan="2" style="text-align: center; background:  #2874a6;">
                                                    <h6 class="mb-0 text-uppercase" style="color: #fff;">Expenses Report<br><small>{{$duration}}</small></h6>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12 border-bottom pb-0">
                                    <table class="items mt-0">
                                        <tr>
                                            <td style="width: 40%; text-align: right; padding-left: 20px;">
                                                @if(!is_null($shop->logo_location))
                                                <figure>
                                                    <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="" width="200">
                                                </figure>
                                                @endif
                                            </td>
                                            <td style="width: 60%;">
                                                <strong style="font-size: 14px;">{{$shop->name}}.</strong><br>
                                                <small style="font-size: 12px;">{{$shop->short_desc}}</small><br> <small>{{$shop->postal_address}} {{$shop->physical_address}} {{$shop->street}} {{$shop->district}}, {{$shop->city}}<br> Email: <b>{{$shop->email}}</b><br> Tel: <b>{{$shop->tel}}</b> Phone: <b>{{$shop->mobile}}</b><br>TIN: <b>{{$shop->tin}}</b> VRN: <b>{{$shop->vrn}}</b></small>
                                            </td>
                                        </tr>
                                    </table>
                                </div>    
                                <div class="col-xs-12">
                                    <table class="items mt-0">
                                        <thead>
                                            <tr>
                                                <th style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.expense_type')}} / {{trans('navmenu.category')}}</th>
                                                <th style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.amount')}}</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <?php $totalexpenses = 0; ?>
                                    <div class="accordion" id="accordionExample">
                                        @foreach($catexpenses as $catexp)
                                        <?php $totalexpenses += $catexp['amount']; ?>
                                        <div class="accordion-item">
                                            <ul class="list-group list-group-unbordered">
                                                <li class="list-group-item accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                                    {{$catexp['name']}} <span class="float-end">
                                                    {{number_format($catexp['amount'], 2, '.', ',')}} <i class="fa fa-caret-down"></i></span>
                                                </li>
                                            </ul>
                                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <ul class="list-group list-group-unbordered">
                                                        @foreach($catexp['expenses'] as $expense)
                                                        <li class="list-group-item">
                                                            {{$expense['expense_type']}} <span class="float-end">
                                                            {{number_format($expense['amount'], 2, '.', ',')}}</span>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <ul class="list-group list-group-unbordered">
                                        @foreach($uncatexpenses as $expense)
                                        <?php $totalexpenses += $expense['amount']; ?>
                                        <li class="list-group-item">
                                            {{$expense['expense_type']}} <span class="float-end">
                                            {{number_format($expense['amount'], 2, '.', ',')}}</span>
                                        </li>
                                        @endforeach
                                        <li class="list-group-item" style="border-top: 2px solid #BDBDBD; border-bottom: 2px solid #BDBDBD;"><b>{{trans('navmenu.total_expenses')}} <span class="float-end">{{number_format($totalexpenses, 2, '.', ',')}}</span></b></li>
                                    </ul>
                                </div>
                            </div>
                            <div style="margin-top: 10px;">
                                <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm" style="margin: 5px;"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> Print</a>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="total-expenses" role="tabpanel">
                            <div class="col-xs-12 table-responsive">
                                <table id="texpenses" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>{{trans('navmenu.category')}}</th>
                                            <th style="text-align: right;">{{trans('navmenu.total')}} {{trans('navmenu.amount')}}</th>  
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($catexpenses as $catexp)
                                        <tr>
                                            <td>{{$catexp['name']}}</td>
                                            <td style="text-align: right;">{{number_format($catexp['amount'], 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th><b>{{trans('navmenu.total')}}</b></th>
                                            <th style="text-align: right;"><b>{{number_format($totalexpenses, 2, '.', ',')}}</b></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="expenses-report" role="tabpanel">
                            <div class="col-xs-12 table-responsive">
                                <table id="all-expenses" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th>{{trans('navmenu.expense_type')}}</th>
                                            <th style="text-align: right;">{{trans('navmenu.amount')}}</th>  
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total = 0; ?>
                                        @foreach($texpenses as $index => $expense)
                                        <?php $total += $expense['amount']; ?>
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td><a href="{{url('single-expense-report/'.$expense['expense_type'])}}">{{$expense['expense_type']}}</a></td>
                                            <td style="text-align: right;">{{number_format($expense['amount'])}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th><b>{{trans('navmenu.total')}}</b></th>
                                            <th style="text-align: right;"><b>{{number_format($total, 2, '.',',')}}</b></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(function(){
            var d = new Date();
            const months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
            var day = d.getDate();
            var month = d.getMonth();
            var year = d.getFullYear();
            var date = day + " " + months[month] + " " + year;
            var duration = "<?php echo $duration; ?>";
            var shop_name = "<?php echo $shop->name; ?>";

            var texptable = $('#texpenses').DataTable({
                "scrollX": true,
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.total_expense_report') }}_" + date,
                        title: "{{ trans('navmenu.total_expense_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.total_expense_report') }}_" + date,
                        title: shop_name + "\n {{ trans('navmenu.total_expense_report') }} \n"+duration,
                    }
                ],
            });
            texptable.buttons().container().appendTo('#texpenses_wrapper .col-md-6:eq(1)');

            var exptable = $('#all-expenses').DataTable({
                "scrollX": true,
                "order": [
                    [1, "asc"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.expense_report') }}_" + date,
                        title: "{{ trans('navmenu.expense_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.expense_report') }}_" + date,
                        title: shop_name + "\n {{ trans('navmenu.expense_report') }} \n"+duration
                    }
                ],
            });
            exptable.buttons().container().appendTo('#all-expenses_wrapper .col-md-6:eq(1)');

        });
    </script>
@endsection
    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">
        function printDiv(divID) {
            //Get the HTML of div
            var divElements = document.getElementById(divID).innerHTML;
            //Get the HTML of whole page
            var oldPage = document.body.innerHTML;
            //Reset the page's HTML with div's HTML only
            document.body.innerHTML = divElements;
            //File name for printed ducument
            document.title = "<?php echo trans('navmenu.debtor_account_stmt').'_'.$reporttime; ?>";
            //Print Page
            window.print();
            //Restore orignal HTML
            document.body.innerHTML = oldPage;
        }

        function savePdf() {
            const element = document.getElementById("total-expenses-pdf");
            var filename = "<?php echo trans('navmenu.total_expense_report').'_'.$reporttime; ?>";
            var opt = {
                margin:       0.5,
                filename:     filename+'.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            // New Promise-based usage:
            html2pdf().set(opt).from(element).toPdf().save();
        }
    </script>