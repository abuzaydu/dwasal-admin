@extends('layouts.app')
@section('page-styles')
@endsection

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item">Ouotes & Orders</li>
                    <li class="breadcrumb-item"><a href="{{ url('orders') }}"> Orders</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                <form class="row g-1" method="POST" action="{{ url('update-order-status') }}">
                	@csrf
                	<div class="col-md-4">
                		<label class="form-label">Order Status</label>
                	</div>
                	<input type="hidden" name="order_id" value="{{$order->id}}">
                	<div class="col-md-4">
                		<select name="status" class="form-select form-select-sm mb-1" onchange="this.form.submit()">
                			@foreach($statuses as $status)
                			@if($order->status == $status)
                			<option selected>{{$status}}</option>
                			@else
                			<option>{{$status}}</option>
                			@endif
                			@endforeach
                		</select>
                	</div>
                    <div class="col-md-4">
                        <a href="{{ route('order-deliveries.create') }}" class="btn btn-primary btn-sm pull-right"> Create New Delivery</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 col-sm-12 mx-auto">
        	<div class="card">
        		<div class="card-body">
                    <ul class="nav nav-tabs-new2">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab_0-0"><i class='fa fa-list'></i> Order Details </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab_1-1"><i class='fa fa-list-alt'></i> Order Processing</a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="tab_0-0" role="tabpanel">
                			<div class="bg-inner cart-section order-details-table">
                				<div class="row g-4">
                					<div class="col-xl-8">
                						<div class="card-details-title">
                							<h6>Order Number #<span>{{$order->id}}</span></h6>
                						</div>
                						<div class="table-details">
                                        	<table class="table table-bordered" style="width: 100%;">
                                        		<thead>
                                        			<tr>
                                        				<th colspan="2">Item Description</th>
                                        				<th>Quantity</th>
                                        				<th>Price</th>
                                        			</tr>
                                        		</thead>
                                        		<tbody>
                                					@foreach($orderitems as $key => $item)
                                        			<tr class="table-order">
                                        				<td>
                                        					<a href="javascript:void(0)">
                                        						<img src="{{ asset('storage/'.$item->image_url)}}" width="60" class="img-fluid blur-up lazyload" alt="">
                                        					</a>
                                        				</td>
                                        				<td>{{$item->name}}</td>
                                        				<td>{{$item->quantity+0}}</td>
                                        				<td>{{number_format($item->price, 2, '.',',')}}</td>
                                        			</tr>
                                        			@endforeach
                                        		</tbody>
                                        		<tfoot>
                                        			<tr class="table-order">
                                        				<td colspan="3">
                                        					<h6>Subtotal :</h6>
                                        				</td>
                                        				<td>
                                        					<h6>{{number_format($order->total,2,'.', ',') }}</h6>
                                        				</td>
                                        			</tr>
                                        			<tr class="table-order">
                                        				<td colspan="3">
                                        					<h6>Shipping :</h6>
                                        				</td>
                                        				<td>
                                        					<h6>{{number_format($order->delivery_cost, 2,'.', ',')}}</h6>
                                        				</td>
                                        			</tr>
                                        			<tr class="table-order">
                                        				<td colspan="3">
                                        					<h6>Tax(VAT) :</h6>
                                        				</td>
                                        				<td>
                                        					<h6>{{number_format($order->tax_amount, 2,'.', ',')}}</h6>
                                        				</td>
                                        			</tr>
                                        			<tr class="table-order">
                                        				<td colspan="3">
                                        					<h4 class="theme-color fw-bold">Total Price :</h4>
                                        				</td>
                                        				<td>
                                        					<h4 class="theme-color fw-bold">{{number_format($order->total+$order->delivery_cost+$order->tax_amount, 2,'.', ',')}}</h4>
                                        				</td>
                                        			</tr>
                                        		</tfoot>
                                        	</table>
                                        </div>
                                    </div>
                                    <div class="col-xl-4">
                                    	<div class="row g-4">
                                    		<div class="col-12">
                                    			<div class="order-success-sec">
                                    				<h4>summery</h4>
                                    				<ul class="order-details">
                                    					<li>Order ID: {{$order->uuid}}</li>
                                    					<li>Order Date: {{ date('F d, Y', strtotime($order->created_at))}}</li>
                                    					<li>Order Total: {{number_format($order->total+$order->delivery_cost+$order->tax_amount, 2,'.', ',')}}</li>
                                    					<li>Order Status: {{$order->status}}</li>
                                    				</ul>
                                    			</div>
                                    		</div>
                                    		<div class="col-12">
                                    			<div class="order-success-sec">
                                    				<h4>shipping address</h4>
                                    				<ul class="order-details">
                                    					<li>Plus Code : <b>{{$address->plus_code}}</b></li>
                                                        @if(!is_null($address->address))
        			                                    <li>{{$address->address}}.</li>
                                                        @endif
        			                                    <li>{{$address->locality}}, {{$address->country}}</li>
        			                                    <li>Contact No. {{$billaddress->phone}}</li>
        			                                    <li>Email : {{$billaddress->email}}</li>
                                    				</ul>
                                    			</div>
                                    		</div>
                                    		<div class="col-12">
                                    			<div class="order-success-sec">
                                    				<div class="payment-mode">
                                    					<h4>payment method</h4>
                                    					<p></p>
                                    				</div>
                                    			</div>
                                    		</div>
                                    		<div class="col-12">
                                    			<div class="order-success-sec">
                                    				<div class="delivery-sec">
                                    					<h3>expected date of delivery: <span>{{ date('F d, Y', $order->exp_delivery_time) }}</span></h3>
                                    					<a href="order-tracking.html">track order</a>
                                    				</div>
                                    			</div>
                                    		</div>
                                    	</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_1-1" role="tabpanel">
                            <div class="bg-inner cart-section order-details-table">
                                <div class="row g-4">
                                    <div class="col-xl-8">
                                        <div class="card-details-title">
                                            <h6>Order Deliveries</h6>
                                            <div class="p-4 border rounded">
                                                <form class="form row g-3" method="POST" action="{{ route('order-deliveries.store')}}" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="order_detail_id" value="{{$order->id}}">
                                                    <div class="col-md-3">
                                                        <label class="form-label">Vehicle<span style="color: red; font-weight: bold;">*</span></label>
                                                        <select name="vehicle_id" class="form-select form-select-sm mb-1" required>
                                                            <option value="">--Select--</option>
                                                            @foreach($vehicles as $key => $vehicle)
                                                            @if($vehicles->count() == 1)
                                                            <option value="{{$vehicle->id}}" selected>{{$vehicle->plate_no}}</option>
                                                            @else
                                                            <option value="{{$vehicle->id}}">{{$vehicle->plate_no}}</option>
                                                            @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 pt-4">
                                                        <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">Create Delivery</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="table-details">
                                            <table class="table table-bordered" style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>Item Description</th>
                                                        <th>Quantity</th>
                                                        <th>Vehicle/Truck</th>
                                                        <th>Time Loaded</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($orderdeliveries as $key => $oditem)
                                                    <tr class="table-order">
                                                        <td>{{$oditem->name}}</td>
                                                        <td>{{$oditem->quantity+0}} {{$oditem->uom}}</td>
                                                        <td>{{$oditem->plate_no }}</td>
                                                        <td>{{$oditem->created_at}}</td>
                                                        <td style="text-align: center;">
                                                            <a href="{{route('order-deliveries.edit', encrypt($oditem->id))}}">
                                                                <i class="fa fa-edit" style="color: blue;"></i>
                                                            </a> | 
                                                            <form method="POST" action="{{route('order-deliveries.destroy' , encrypt($oditem->id))}}" id="delete-form-{{$key}}" style="display: inline;"> 
                                                                @csrf
                                                                @method('DELETE')
                                                                <a href="javascript:;" onclick="return confirmDelete({{$key}})">
                                                                    <i class="fa fa-trash" style="color: red;"></i>
                                                                </a>                        
                                                            </form>    
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-xl-4">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection