@extends('layouts.inv')
@section('content')
    <script type="text/javascript">
        function confirmApproval(id) {
            Swal.fire({
                title: 'Are you sure to approve the requested Petty Cash?',
                showDenyButton: true,
                confirmButtonText: 'Yes Approve',
                denyButtonText: `Don't Approve`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    window.location.href = 'approve-petty-cash/'+id;
                    Swal.fire('Approved!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('Not Approved', '', 'info')
                }
            })
        }
    </script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item"><a href="{{ url('trip-logs') }}">Trip Logs</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>        
            <div class="col-lg-8 col-md-8 col-sm-12 text-right">
               
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row g-1">
        <div class="col-xl-10 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row g-1 print_invoice" id="payslip">
                        <div class="col-md-12">
                            <table class="table table-striped display nowrap" style="width: 100%;">
                                <tbody>
                                    <tr style="border-bottom: 2px solid gray;">
                                        <th colspan="4">Trip Details</th>
                                    </tr>
                                    <tr>
                                        <td>Client</td>
                                        <th>@if(!is_null($customer)){{$customer->name}}@endif</th>
                                        <td>Vehicle</td>
                                        <th>{{$trip->device_number}} - {{$trip->device_name}}</th>
                                    </tr>
                                    <tr>
                                        <?php
                                            $difference = 0;
                                            if (!is_null($trip->trip_end_date)) {
                                                $difference = abs(strtotime($trip->trip_end_date) - strtotime($trip->trip_date))/3600;
                                            }
                                        ?>
                                        <td>Trip Start Date</td>
                                        <th>{{ date('d/m/Y H:i', strtotime($trip->trip_date))}}</th>
                                        <td>Trip End Date</td>
                                        <th>{{ date('d/m/Y H:i', strtotime($trip->trip_end_date))}}</th>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td>Time Duration</td>
                                        <th>{{ $difference }} Hours</th>
                                    </tr>
                                    <tr>
                                        <td>Trip from</td>
                                        <th>{{$trip->from}}</th>
                                        <td>Trip To</td>
                                        <th>{{$trip->to }}</th>
                                    </tr>
                                    <tr>
                                        <td>Mileage Start</td>
                                        <th>{{$trip->mileage_out+0}} Kms</th> 
                                        <td>Mileage Finish</td>
                                        <th>{{$trip->mileage_in+0}} Kms</th>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td>Dist Traveled</td>
                                        <th>{{$trip->mileage_in-$trip->mileage_out}} Kms</th>
                                    </tr>
                                    <tr>
                                        <td>Fuel Start (Ltrs)</td>
                                        <th>{{$trip->fuel_start+0}}</th>
                                        <td>Fuel End (Ltrs)</td>
                                        <th>{{$trip->fuel_end+0}}</th>
                                    </tr>
                                    <tr>
                                        <td>Fuel Used</td>
                                        <th>{{$trip->fuel+0}}</th>
                                        <td>Fuel Cost</td>
                                        <th>{{ number_format($trip->fuel*$trip->fuel_unit_cost, 2, '.', ',') }}</th>
                                    </tr>
                                    <tr>
                                        <td>Trip Description</td>
                                        <th>{{$trip->trip_title}}</th>
                                        <td>Trip Price</td>
                                        <th>{{$currency}} {{ number_format($trip->trip_price, 2, '.', ',') }}</th>
                                    </tr>
                                    <tr style="border-bottom: 2px solid gray;">
                                        <th colspan="4">Container Details</th>
                                    </tr>
                                    <tr>
                                        <td>Container No.</td>
                                        <th>{{$trip->container_no}}</th> 
                                        <td>Container Size</td>
                                        <th>{{$trip->container_size}}</th>
                                    </tr>
                                    <tr>
                                        <td>Bill No.</td>
                                        <th>{{$trip->bill_no}}</th> 
                                        <td>Shipping</td>
                                        <th>{{$trip->shipping}}</th>
                                    </tr>
                                    <tr>
                                        <td>Gross Weight</td>
                                        <th>{{$trip->gross_weight}}</th> 
                                        <td>Net Weight</td>
                                        <th>{{$trip->net_weight}}</th>
                                    </tr>
                                    <tr>
                                        <td>Load Type</td>
                                        <th>{{$trip->load_type}}</th> 
                                        <td>Is Transit</td>
                                        <th>@if($trip->is_transit) Yes @else No @endif</th>
                                    </tr>
                                    <tr style="border-bottom: 2px solid gray;">
                                        <th colspan="4">Entry Person & Time</th>
                                    </tr>
                                    <tr>
                                        <td>Entry Date</td>
                                        <th>{{ date('d/m/Y H:i:s', strtotime($trip->created_at))}}</th>
                                        <td>Entry Person</td>
                                        <th>{{$trip->first_name}} {{$trip->last_name}}</th>
                                    </tr>
                                    @if(!is_null($trip->an_sale_id))
                                    <?php $sale = App\Models\AnSale::where('an_sales.id', $trip->an_sale_id)->join('users', 'users.id', '=', 'an_sales.user_id')->select('invoice_no', 'an_sales.created_at as created_at', 'first_name', 'last_name')->first(); ?>
                                    <tr style="border-bottom: 2px solid gray;">
                                        <th colspan="4">Invoice Details</th>
                                    </tr>
                                    <tr>
                                        <td>Invoice No</td>
                                        <th>{{ sprintf('%06d', $sale->invoice_no)}}</th>
                                        <td>Invoiced At</td>
                                        <th>{{ date('d/m/Y H:i:s', strtotime($sale->created_at))}}</th>
                                    </tr>
                                    <tr>
                                        <td>Invoiced By</td>
                                        <th colspan="3">{{$trip->first_name}} {{$trip->last_name}}</th>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr/>
                    <div class="text-end">
                        @if(Auth::user()->can('edit-trip-log'))
                        <a href="{{ route('trip-logs.edit', encrypt($trip->id)) }}" class="btn btn-secondary btn-sm">Update Trip Details</a>
                        @endif
                        @if(!is_null($trip->to) || !is_null($trip->trip_end_date) || !is_null($trip->mileage_in))
                        @if(is_null($trip->an_sale_id))
                        @if(Auth::user()->can('create-invoice'))
                        <a href="{{ url('create-invoice-for-trips/'.encrypt($customer->id))}}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Create Invoice Trips</a>
                        @endif
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection