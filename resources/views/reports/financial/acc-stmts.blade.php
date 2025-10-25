@extends('layouts.gen')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('reports') }}">Reports </a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                <form class="dashform row g-3" action="{{url('account-statements')}}" method="POST">
                    @csrf
                    <div class="col-md-5">
                        <select name="account_id" class="form-select form-select-sm mb-1" onchange='this.form.submit();'>
                            <option value="">All Accounts</option>
                            @foreach($accounts as $acc)
                            @if(!is_null($account) && $acc->id == $account->id)
                            <option value="{{$acc->id}}" selected>{{$acc->account_name}}@if(!is_null($acc->account_number)) ({{$acc->account_number}})@endif @if(!is_null($acc->bank_name)) -{{$acc->bank_name}}@endif</option>
                            @else
                            <option value="{{$acc->id}}">{{$acc->account_name}}@if(!is_null($acc->account_number)) ({{$acc->account_number}})@endif @if(!is_null($acc->bank_name)) -{{$acc->bank_name}}@endif</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                    <!-- Date and time range -->
                    <div class="col-md-7">
                        <div class="input-group">
                            <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
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

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div id="invoice-view">
                        <div class="row g-1 print_invoice" id="print-stmt">
                            <div class="col-md-12">
                                <table style="width: 100%;">
                                    <tr>
                                        <td colspan="9" style="text-align: center;">
                                            <span style="font-size: 18px">{{$company->name}} <br>(<b>{{$shop->name}}</b>)</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="9" style="background: #0459c6; padding-left: 15px;  border-radius: 0px; text-align: center; color: #fff; font-size: 20px; text-transform: uppercase;">
                                            <span> {{ $title }}</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-12 customer mt-2 mb-0">
                                <div class="row">
                                    <div class="col-md-7" style="padding-left: 30px;">
                                        Account Name : <b>{{$account_name}}</b><br>
                                    </div>
                                    <div class="col-md-5">
                                        <table class="items mt-0">
                                            <tbody>
                                                <tr>
                                                    <td>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <table class=" mt-0" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: left; border-bottom: 2px solid gray;">Date/Time</th>
                                            <th style="text-align: left; border-bottom: 2px solid gray;">{{trans('navmenu.description')}}</th>
                                            <th style="text-align: center; border-bottom: 2px solid gray;">Debit</th>
                                            <th style="text-align: center; border-bottom: 2px solid gray;">Credit</th>
                                            <th style="text-align: center; border-bottom: 2px solid gray;">{{trans('navmenu.balance')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- <tr>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><b>Bal Before {{date('d M, Y', strtotime($start_date))}}</b></td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">-</td>
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($bal_before_start, 2, '.', ',')}}</b></td>
                                        </tr> -->
                                        <?php $balance = 0; $tdebit = 0; $tcredit = 0; ?> 
                                        @foreach($astmts as $index => $trans)
                                        <?php $balance += $trans->debit-$trans->credit;
                                            $tdebit += $trans->debit; $tcredit += $trans->credit; ?>
                                        <tr>
                                            <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{date('d-m-Y H:i:s', strtotime($trans->date))}}</td>
                                            <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{ $trans->description}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($trans->debit,2, '.', ',')}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($trans->credit,2, '.', ',')}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($balance,2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th style="text-align: center;"></th>
                                            <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                            <th style="text-align: center;">{{number_format($tdebit,2, '.', ',')}}</th>
                                            <th style="text-align: center;">{{number_format($tcredit,2, '.', ',')}}</th>
                                            <th style="text-align: center;">{{number_format($balance,2, '.', ',')}}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm" style="margin: 5px;"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> Print</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<link rel="stylesheet" type="text/css" href="{{ asset('css/receipt.css') }}">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js" integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous"></script>
    <script>    
        $(document).ready( function ()  {
            $('#btnPrint').on("click", function(e) {

                if ($("#printer").length) {
                    $("#printer").remove();
                }

                var divElements = $("#receipt").html();
                var iframe = $('<iframe class="hidden" id="printer"></iframe>').appendTo('body');
                var printer = $('#printer');
                printer.contents().find('body').append('<!DOCTYPE html><head><title>Print Title</title><link href="https://fonts.cdnfonts.com/css/lt-binary-neue" rel="stylesheet"></head><body>' + divElements + '</body>');
                setTimeout(function() {  
                    document.title = "<?php echo 'ACC Statement_'.$reporttime ?>";
                    printer.get(0).contentWindow.print();

                }, 250);
            });
        });
    </script>

    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">

        function savePdf() {
          const element = document.getElementById("print-stmt");
          var filename = "<?php echo trans('navmenu.title').'_'.$reporttime; ?>";
          var opt = {
              margin:       0.5,
              filename:     filename+'.pdf',
              image:        { type: 'jpeg', quality: 0.98 },
              html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
              jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

          // New Promise-based usage:
          html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                window.open(pdf.output('bloburl'), '_blank');
            });
          
        }
    </script>