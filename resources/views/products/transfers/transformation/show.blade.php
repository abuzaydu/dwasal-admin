@extends('layouts.inv')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item"><a href="{{url('transfer-orders')}}">{{trans('navmenu.stock_transfer')}}</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

	<div class="card radius-10">
		<div class="card-header" style="border-bottom: 2px solid #E3F2FD;">
            <div class="btn-group float-end">
            	<a href="{{route('transformation-transfer.edit', encrypt($transorder->id))}}" class="btn bg-info " style="margin: 5px;"><i class="fa fa-edit" ></i> Edit</a>
            	<a href="#" onclick="javascript:savePdf()" class="btn bg-warning" style="margin: 5px;"><i class="fa fa-download"></i> Download PDF</a>
            	<a href="#" onclick="javascript:printDiv('order-content')" class="btn bg-success" style="margin: 5px;"><i class="fa fa-printer"></i> {{trans('navmenu.print')}}</a>
			</div>
		</div>
		<div class="card-body">
			<div id="order-content">
				<div class="clearfix order-header">
					@if(!is_null($source->logo_location))
	                <figure>
	                  <img class="invoice-logo" src="{{asset('storage/logos/'.$source->logo_location)}}" alt="">
	                </figure>
	                @endif
					<div class="float-end">
						<h4 class="title">{{$source->name}} <br>
				        <small style="font-size: 12px;">{{$source->short_desc}}</small></h4>
				        <p>
				        	{{$source->postal_address}} {{$source->physical_address}}
				            {{$source->street}} {{$source->district}}, {{$source->city}}<br>
				            E-Mail: <a href="#">{{$source->email}}</a>
				            Tel: <a href="#">{{$source->mobile}}</a>
				            Web: <a href="#">{{$source->website}}</a>
				        </p>
					</div>
					<div class="order-date">
						<table>
							<tr>
								<td class="name" style="font-weight: bold;">{{trans('navmenu.date')}} : </td>
								<td class="value">{{date("d, M Y", strtotime($transorder->created_at))}}</td>
							</tr>
							<tr>
								<td class="name" style="font-weight: bold;">{{trans('navmenu.transfer_date')}} : </td>
								<td class="value">{{date("d, M Y", strtotime($transorder->order_date))}}</td>
							</tr>
						</table>
					</div>
				</div>
				

				<div class="order-content">
					<div class="order-details clearfix">
						<div class="order-source pb-4">
							<div style="text-transform: uppercase;">{{trans('navmenu.source_shop')}}:
								<span class="font-15" style="color: blue;">  {{$source->name}}</span>
							</div>
							<div>{{$source->street}} {{$source->city}}</div>
						</div>
						<div class="order-destin">
							<p style="text-transform: uppercase;">{{trans('navmenu.destin_shop')}}: <span class="font-15" style="color: blue;">{{$destin->name}}</span></p>
							
							<div>{{$destin->street}} {{$destin->city}}</div>
						</div>
						<div class="order-reason">
							<div><strong class="font-18">{{trans('navmenu.reason')}}:</strong></div>
							<div style="color: blue;">{{$transorder->reason}}</div>
						</div>
					</div>
					<div class="col-md-12 order-items">
						<h5>{{trans('navmenu.product_transferred')}}</h5>
						<table border="0" cellspacing="0" cellpadding="0" class="item-table">
				            <thead>
				            	<tr>
				            		<th class="Item">{{trans('navmenu.item_name')}}</th>
					             	<th class="qty">{{trans('navmenu.transfer_qty')}}</th>
				             	</tr>
				            </thead>
				            <tbody>
				             	<tr>
				             		<td class="item"><h6>{{$transorder->name}}</h6></td>
					                <td class="qty">{{number_format($transorder->source_product_quantity)}}</td>
					            </tr>
					           
					        </tbody>
					    </table>

					</div>

					<div class="col-md-12 order-items">
						<h5>{{trans('navmenu.new_products_made')}}</h5>
						<table border="0" cellspacing="0" cellpadding="0" class="item-table">
				            <thead>
				            	<tr>
				            		<th class="Item">{{trans('navmenu.item_name')}}</th>
					             	<th class="qty">{{trans('navmenu.transfer_qty')}}</th>
				             	</tr>
				            </thead>
				            <tbody>
				             	@foreach($orderitems as $key => $item)
				             	<tr>
				             		<td class="item"><h6>{{$item->product->name}}</h6></td>
					                <td class="qty">{{number_format($item->quantity)}}</td>
					            </tr>
					            @endforeach
					        </tbody>
					    </table>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="clearfix order-bottom " style="margin-top: 15px;">
					<div class="issuer" style="width: 50%; float: left; padding-left: 55px;">
						<p>
							<span style="text-transform: uppercase; font-size: 18px; font-weight: bold;">{{trans('navmenu.transfer_by')}}</span><br>
							{{trans('navmenu.name')}} : <strong>{{$user->first_name}} {{$user->last_name}}</strong><br>
							{{trans('navmenu.signature')}} <strong>.....................</strong>
						</p>						
					</div>
					<div class="receiver" style="width: 50%; float: right; padding-left: 55px;">
						<p>
							<span style="text-transform: uppercase; font-size: 18px; font-weight: bold;">{{trans('navmenu.stock_received_by')}}</span><br>
							{{trans('navmenu.name')}} : <strong>.........................</strong><br>
							{{trans('navmenu.signature')}} <strong>.....................</strong>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection

<script language="javascript" type="text/javascript">
        function printDiv(divID) {

            //Get the HTML of div
            var divElements = document.getElementById(divID).innerHTML;
            //Get the HTML of whole page
            var oldPage = document.body.innerHTML;

            //Reset the page's HTML with div's HTML only
            document.body.innerHTML = divElements;

            //File name for printed ducument
            document.title = "<?php echo $title.'_no_'.$transorder->order_no.'_'.$transorder->order_date; ?>";
            //Print Page
            window.print();

            //Restore orignal HTML
            document.body.innerHTML = oldPage;
        }

        function savePdf() {
          	const element = document.getElementById("order-content");
         	var filename = "<?php echo 'Stock Transfer Order_'.sprintf('%05d', $transorder->order_no).'_'.$transorder->created_at; ?>";
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