@extends('layouts.gen')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('reports') }}">Reports </a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">

        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr/>
            <form class="dashform row g-3" action="{{url('open-closing-amount-statement')}}" method="POST">
                @csrf
                <div class="col-md-6"></div>
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                <!-- Date and time range -->
                <div class="col-md-6 mb-3">
                    <div class="input-group">
                        <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                            <span><i class="fa fa-calendar"></i></span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-body">
                    <div id="inv-content">
                        <div class="col-md-12" style="text-align: center; text-transform: uppercase; color: blue">
                            @if(!is_null($shop->logo_location))
                            <figure>
                                <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                            </figure>
                            @endif
                            <h5>{{$shop->name}}</h5>
                            <h6 class="title">{{trans('navmenu.oca_stmt')}}<br> <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h6>
                        </div>
                        <div class="col-md-12" style="border-top: 2px solid #82B1FF;">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th>{{trans('navmenu.opening')}}</th>
                                            <th style="border-right: 1px solid #82B1FF;"></th>
                                            <th></th>
                                            <th>{{trans('navmenu.closing')}}</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <thead style="background:#cfd8dc !important;">
                                        <tr>
                                            <th>{{trans('navmenu.date')}}</th>
                                            <th>{{trans('navmenu.float')}}</th>
                                            <th>{{trans('navmenu.cash')}}</th>
                                            <th style="border-right: 1px solid #82B1FF;">{{trans('navmenu.total')}}</th>
                                            <th>{{trans('navmenu.float')}}</th>
                                            <th>{{trans('navmenu.cash')}}</th>
                                            <th>{{trans('navmenu.total')}}</th>
                                            <th>{{trans('navmenu.loss')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ocamounts as $oca)
                                        <tr>
                                            <td>{{$oca['date']}}</td>
                                            <td>{{number_format($oca['ot_float'], 2, '.', ',')}}</td>
                                            <td>{{number_format($oca['ot_cash'], 2, '.', ',')}}</td>
                                            <td>{{number_format($oca['ot_cash']+$oca['ot_float'], 2, '.', ',')}}</td>
                                            <td>{{number_format($oca['ct_float'], 2, '.', ',')}}</td>
                                            <td>{{number_format($oca['ct_cash'], 2, '.', ',')}}</td>
                                            <td>{{number_format($oca['ct_cash']+$oca['ct_float'], 2, '.', ',')}}</td>
                                            <td>{{number_format(($oca['ct_cash']+$oca['ct_float'])-($oca['ot_cash']+$oca['ot_float']), 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>  
                    <div class="col-md-12 mb-3 pt-2 options" style="margin-top: 5px;">
                        <!-- <a href="#" onclick="javascript:printDiv('inv-content')" class="btn btn bg-info btn-sm float-end" style="margin-left: 5px;"><i class="fa fa-printer"></i> {{trans('navmenu.print')}}</a> -->
                        <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm  float-end"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> {{trans('navmenu.print')}}</a>
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
        document.title = "<?php echo trans('navmenu.oca_stmt').'_'.$duration; ?>";
        //Print Page
        window.print();
        //Restore orignal HTML
        document.body.innerHTML = oldPage;
    }

    function savePdf() {
        const element = document.getElementById("inv-content");
        var filename = "<?php echo trans('navmenu.oca_stmt').'_'.$reporttime; ?>";
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