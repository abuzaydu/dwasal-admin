@extends('layouts.inv')
<script>
  function confirmDelete(id) {
        Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.value) {
            window.location.href="{{url('delete-transorder-item/')}}/"+id;
            Swal.fire(
              'Deleted!',
              'Your Sale has been deleted.',
              'success'
            )
          }
        })
    }

</script>
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

<div class="row">
	
	<div class="col-md-12">
		<div class="card">
			<form class="form" method="POST" action="{{route('transfer-orders.update', encrypt($transorder->id))}}">
				@csrf
              	{{ method_field('PATCH') }} 
				<div class="card-header" style="border-bottom: 2px solid #BBDEFB;">
					<button class="btn btn-success" type="submit"><i class="fa fa-file"></i> Save Order</button>
					<a href="{{url('transfer-orders')}}" class="btn btn-warning pull-right" id="btn-create" style="margin-right: 5px;"><i class="lni lni-close"></i> Cancel</a>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label">Order No. <span style="color: red; font-weight: bold;">*</span></label>
								<input type="text" name="order_no" class="form-control" placeholder="Enter your order Number" value="TO-{{sprintf('%05d', $transorder->order_no)}}" required readonly>
							</div>
						</div>
						<div class="col-md-6">

								<label class="form-label">Date <span style="color: red; font-weight: bold;">*</span></label>
								<div class="input-group">
									<div class="input-group-text" id="calendar">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" name="order_date" id="orderdatepicker" required class="form-control" placeholder="Select date of transfer" value="{{$transorder->order_date}}" aria-describedby="calendar">
								</div>
						</div>
					</div>
					<div class="row pt-3">
						<div class="col-md-6">
								<label class="form-label">Source Shop/Store <span style="color: red; font-weight: bold;">*</span></label>
								<input type="text" name="shop_id" value="{{$shop->name}}" readonly="" class="form-control">
						</div>
						<div class="col-md-6">
								<label class="form-label">Destination Shop/Store <span style="color: red; font-weight: bold;">*</span></label>
								<select class="form-control" name="destin_id" ng-model="destin_id" ng-change="getDestin(destin_id)"  required>
									<option value="{{$destinshop->id}}">{{$destinshop->display_name}}</option>
									<option value="">Select Shop/Store</option>
									@foreach($destinations as $key => $destin)
									<option value="{{$destin->id}}">{{$destin->name}}</option>
									@endforeach
								</select>
						</div>
					</div>

					<div class="col-md-6 pt-3">
						<div class="form-group">
							<label class="form-label">Reason <span style="color: red; font-weight: bold;">*</span></label>
							<textarea name="reason" class="form-control" required placeholder="Please type here the reason of transfer">{{$transorder->reason}}</textarea>
						</div>
					</div>

					<div class="clearfix pt-2" style="width: 100%; border-bottom: 2px solid #BBDEFB; margin-bottom: 10px;"></div>
				</div>
			</form>
			<div class="col-md-12 order-items ms-2 me-2 ">
				<h3>Order Items</h3>
				<table border="0" cellspacing="0" cellpadding="0" class="table item-table"  >
					<thead>
						<tr>
							<th class="Item">Item name</th>
					        <th class="source">Source Stock</th>
					        <th class="destin">Destin Stock</th>
					        <th class="qty">Quantity</th>
				        </tr>
					</thead>
					<tbody>
						@foreach($orderitems as $key => $item)
						<tr>	
							<td class="item"><h4>{{$item->product->name}}</h4></td>
							<td class="source">{{number_format($item->source_stock)}}</td>
					        <td class="destin">{{number_format($item->destin_stock)}}</td>
							<form id="item-form{{$key}}" class="form" method="POST" action="{{url('update-transorder-item')}}">
								<td class="qty">
									@csrf
									<input type="hidden" name="id" value="{{$item->id}}">
									<input type="hidden" name="transfer_order_id" value="{{$transorder->id}}">
									<input id="input_qty{{$key}}" type="number" name="quantity" value="{{number_format($item->quantity)}}" style="text-align: center;">
									<script>
										$("#input_qty"+"<?php echo $key ?>").blur(function() { 
											$("#item-form"+"<?php echo $key ?>").submit(); 
										});
									</script>
								</td>
							</form>
							<td class="del">
								<a href="#" onclick="confirmDelete('<?php echo encrypt($item->id) ?>')"><i class="fa fa-trash"  style="color: red;"></i></a>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
	
</div>
@endsection