@extends('layouts.gen')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('reports') }}">Reports </a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right pt-0">
                <form class="dashform row g-3" action="{{url('income-statement')}}" method="POST">
                    @csrf
                    <div class="col-md-5">
                        <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm  float-end"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> {{trans('navmenu.print')}}</a>
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="col-md-7 float-end">
                        <div class="input-group">
                            <button type="button" class="btn btn-white pull-right" id="reportrange">
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
                    <div id="inv-content">
                        <div class="col-md-12" style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 3px sold red;">
                            @if(!is_null($shop->logo_location))
                            <figure>
                                <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="" width="200">
                            </figure>
                            @endif
                            <h5>{{$shop->name}}</h5>
                            <P style="font-size: 8px !important; color: #000; text-transform: capitalize;">{{$shop->short_desc}}<br> {{$shop->postal_address}} {{$shop->physical_address}} @if(!is_null($shop->street)){{$shop->street}},@endif @if(!is_null($shop->district)){{$shop->district}},@endif {{$shop->city}}<br> Email: <b>{{$shop->email}}</b> Tel: <b>{{$shop->tel}}</b> Phone: <b>{{$shop->mobile}}</b><br>TIN: <b>{{$shop->tin}}</b> VRN: <b>{{$shop->vrn}}</b></P>
                            <span>{{trans('navmenu.income_stmt')}} <BR><b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></span>
                        </div>
                        <div class="col-md-12" style="border-top: 2px solid #82B1FF; padding: 35px;">
                            <h6 class="mb-3 text-uppercase text-center"><b>{{trans('navmenu.revenue')}}</b></h6>
                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item">
                                  {{trans('navmenu.sales')}} <span class="float-end">{{number_format($total_sales, 2, '.', ',')}}</span>
                                </li>
                                <li class="list-group-item">
                                  {{trans('navmenu.cost_of_sales')}} <span class="float-end">{{number_format($total_co_sales, 2, '.', ',')}}</span>
                                </li>
                                <li class="list-group-item" style="border-top: 2px solid #BDBDBD; border-bottom: 2px solid #BDBDBD;">
                                  <b>{{trans('navmenu.gross_profit')}} <span class="float-end">{{number_format($total_sales-$total_co_sales, 2, '.', ',')}}</span></b>
                                </li>
                            </ul>
                            <h6 class="mb-3 text-uppercase text-center pt-3"><b>{{trans('navmenu.expenses')}}</b></h6>
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
                                <?php $totalexpenses += $expense->amount; ?>
                                <li class="list-group-item">
                                    {{$expense->expense_type}} <span class="float-end">
                                    {{number_format($expense->amount, 2, '.', ',')}}</span>
                                </li>
                                @endforeach
                                <li class="list-group-item" style="border-top: 2px solid #BDBDBD; border-bottom: 2px solid #BDBDBD;"><b>{{trans('navmenu.total_expenses')}} <span class="float-end">{{number_format($totalexpenses, 2, '.', ',')}}</span></b></li>
                            </ul>
                            <h6 class="mb-3 text-uppercase text-center pt-3"><b>{{trans('navmenu.net_profit')}}</b></h6>
                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item" style="border-top: 2px solid #BDBDBD; border-bottom: 2px solid #BDBDBD;">
                                    <b>{{trans('navmenu.profit')}} <span class="float-end">{{number_format(($total_sales-$total_co_sales)-$totalexpenses, 2, '.', ',')}}</span></b>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script type="text/javascript">
    function printDiv(divID) {
        //Get the HTML of div
        var divElements = document.getElementById(divID).innerHTML;
        //Get the HTML of whole page
        var oldPage = document.body.innerHTML;
        //Reset the page's HTML with div's HTML only
        document.body.innerHTML = divElements;
        //File name for printed ducument
        document.title = "<?php echo trans('navmenu.income_stmt').'_'.$duration; ?>";
        //Print Page
        window.print();
        //Restore orignal HTML
        document.body.innerHTML = oldPage;
    }

    function savePdf() {
        const element = document.getElementById("inv-content");
        var filename = "<?php echo trans('navmenu.income_stmt').'_'.$reporttime; ?>";
        var opt = {
            margin:       0.5,
            filename:     filename+'.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).toPdf().save();
        // New Promise-based usage:
        // html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
        //     window.open(pdf.output('bloburl'), '_blank');
        // });
    }
</script>