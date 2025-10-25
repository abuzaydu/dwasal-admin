@extends('layouts.app')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-0">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item"><a href="{{ url('food-productions') }}">Food Productions</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm" style=""><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> Print</a>
                <a href="{{ route('food-productions.edit', encrypt($rmuse->id))}}" class="btn btn-primary btn-sm" style=""><i class="fa fa-edit"></i> Update</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row g-1 print_invoice" id="print-voucher">
                        <div class="col-md-12">
                            <table class="table mb-1">
                                <tbody>
                                    <tr>
                                        <td colspan="2" style="text-align: center; background: #0459c6;">
                                            <h4 class="mb-0 text-uppercase" style="color: #fff;">FOOD PRODUCTION</h4>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12 border-bottom pb-4" style="border-bottom: 1px solid black;">
                            <table class="items mt-0">
                                <tr>
                                    <td style="width: 50%; padding-left: 30px;">
                                        @if(!is_null($shop->logo_location))
                                        <figure>
                                            <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="" width="250" style="border: 1px solid gray;">
                                        </figure>
                                        @endif
                                    </td>
                                    <td style="width: 50%; padding-right: 20px;">
                                        <table class="meta">
                                            <tbody>
                                                <tr>
                                                    <td class="meta-head" style="text-align: right; font-size: 14px;">{{trans('navmenu.date')}} : <b id="date">
                                                    {{date("d, M Y H:i:s", strtotime($rmuse->date))}}</b></td>
                                                </tr>
                                                <tr>
                                                    <td class="meta-head" style="text-align: right; font-size: 14px;">BATCH No:  <b id="date">{{ sprintf('%05d', $rmuse->prod_batch)}}</b></td>
                                                </tr>
                                                <tr>
                                                    <td class="meta-head" style="text-align: right;">Food Type : <b id="date">{{ $rmuse->name }}</b></td>
                                                </tr>
                                            </tbody>
                                        </table>    
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <table class="list-items" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{trans('navmenu.description')}}</th>
                                        <th style="text-align: center;">UOM</th>
                                        <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                        <th style="text-align: right;">{{trans('navmenu.total')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $tqty = 0; ?>
                                    @foreach($uitems as $key => $item)
                                    <?php $tqty += $item->quantity; ?>
                                    <tr>
                                        <td class="no">{{$key+1}}</td>
                                        <td class="text-left">{{$item->name}}</td>
                                        <td style="text-align: center;">{{$item->basic_uom}}</td>
                                        <td style="text-align: center;">{{$item->quantity+0 }}</td>
                                        <td style="text-align: center;">{{number_format($item->unit_cost, 2, '.', ',')}}</td>  
                                        <td style="text-align: right;">{{number_format($item->quantity*$item->unit_cost, 2, '.', ',')}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td style="text-align: center;"><b>{{trans('navmenu.total')}}</b></td>
                                        <td></td>
                                        <td style="text-align: center;"><b>{{$tqty}}</b></td>
                                        <td></td>
                                        <td style="text-align: right;"><b>{{number_format($rmuse->total_cost, 2, '.', ',')}}</b></td>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="notices pt-0">
                                <div>{{trans('navmenu.comments')}}:</div>
                                <div class="notice">{{$rmuse->comments}}</div>
                            </div>
                            <div class="row pt-4">
                                <p class=" col font-12 text-center">
                                    Prepared By : <br>{{trans('navmenu.name')}} : <b>{{$rmuse->first_name}} {{$rmuse->last_name}}</b> <br> {{trans('navmenu.signature')}} _________________ 
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">
        function savePdf() {
            const element = document.getElementById("print-voucher");
            var filename = "<?php echo $title.'_no_'.$rmuse->grn_no.'_'.$rmuse->time_created; ?>";
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