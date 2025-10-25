@extends('layouts.inv')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script type="text/javascript">
    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_delete')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('delete-form-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function createRequest() {
        document.getElementById('st-req-form').submit();
    }
</script>

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                <form class="dashform row g-1" action="{{ url('f-transfer-orders') }}" method="POST" id="stockform">
                    @csrf
                    <div class="col-md-4">
                        <a href="{{ url('transfer-to-item')}}" class="btn btn-primary btn-sm">Transfer To Item</a>
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="col-md-8">
                        <button type="button" class="btn btn-default pull-right" id="reportrange">
                        <span><i class="fa fa-calendar"></i></span>
                        <i class="fa fa-caret-down"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs nav-tabs-new2" role="tablist"  >
                                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab_1"><i class='fa fa-list font-18 me-1'></i> STO Requests </a></li>
                                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_2"><i class='fa fa-list-alt font-18 me-1'></i> STO Returns</a></li>
                                @if(!$shop->is_warehouse)
                                <li class="nav-item">
                                    <a class="nav-link" href="#" onclick="createRequest()" aria-selected="false"><i class='fa fa-plus font-18 me-1'></i> New STO Request</a>
                                    <form id="st-req-form" method="POST" action="{{ url('create-st-request') }}" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="id" value="">
                                    </form>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link"  href="{{route('transfer-orders.create')}}"><i class='fa fa-plus-circle font-18 me-1'></i> New STO TO Other Shops</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content py-1">
                        <form class="row g-1" method="POST" action="{{ url('f-transfer-orders') }}">
                            @csrf
                            <div class="col-md-6">
                                <div class="input-group mb-0">
                                    <input type="text" class="form-control form-control-sm mb-1" name="search_key" placeholder="Search Any Stock transfer. Enter STO Number" autocomplete="off" aria-label="Input Keayword" aria-describedby="button-addon2">
                                    <button class="btn btn-outline-secondar btn-sm" type="submit" id="button-addon2"><i class='fa fa-search'></i> Search</button>
                                </div>
                            </div>
                        </form>
                        <div class="tab-pane fade show active table-responsive" id="tab_1" role="tabpanel">
                            <table id="sto-requests" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>{{trans('navmenu.date')}}</th>
                                        <th>{{trans('navmenu.sto_no')}}</th>
                                        <th>{{trans('navmenu.source_shop')}}</th>
                                        <th>{{trans('navmenu.destin_shop')}}</th>
                                        <th>Requested By</th>
                                        <th>{{trans('navmenu.transfer_by')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.transfer_type')}}</th>
                                        <th>{{trans('navmenu.status')}}</th>
                                        <th>Request Time</th>
                                        <th>Confirm Time</th>
                                        <th>Receive Time</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sto_requests as $i => $order)
                                    <tr>
                                        <td>{{$order->order_date}}</td>
                                        <td> <a href="{{route('transfer-orders.show', encrypt($order->id))}}"> {{ sprintf('%04d', $order->order_no)}}</a></td>
                                        <td>{{\App\Models\Shop::find($order->shop_id)->name}}</td>
                                        <td>{{\App\Models\Shop::find($order->destination_id)->name}}</td>
                                        <td>
                                            @if(!is_null($order->requester_id))
                                            {{App\Models\User::find($order->requester_id)->first_name}}
                                            @else
                                            
                                            @endif
                                        </td>
                                        <td>
                                            @if(!is_null($order->user_id))
                                            {{App\Models\User::find($order->user_id)->first_name}}
                                            @else

                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            @if($order->is_request)
                                            <span class="badge bg-warning">
                                            Request
                                            </span>
                                            @endif
                                            @if($order->is_return)
                                            <span class="badge bg-secondary">
                                            Return
                                            </span>
                                            @endif
                                            @if(!$order->is_request && !$order->is_return)
                                            <span class="badge bg-success">
                                            Normal
                                            </span>
                                            @endif
                                        </td>
                                        <td>{{$order->status}}</td>
                                        <td>{{$order->created_at}}</td>
                                        <td>{{$order->confirm_time}}</td>
                                        <td>{{$order->receive_time}}</td>
                                        <td>
                                            @if(!$order->is_cancelled && $order->status != 'Received')
                                            <a href="{{route('transfer-orders.edit', encrypt($order->id))}}">
                                                <i class="fa fa-edit" style="color: blue;"></i>
                                             Edit </a> @if(Auth::user()->can('cancel-stock-transfer'))| 
                                            <a href="#" class="text-warning" onclick="confirmCancel('<?php echo encrypt($order->id) ?>')"><i class="fa fa-x-circle"></i> Cancel</a> @endif @endif @if($order->is_cancelled && Auth::user()->can('cancel-stock-transfer')) | 
                                            <a href="#" class="text-success" onclick="confirmCancel('<?php echo encrypt($order->id) ?>')"><i class="fa fa-x-circle"></i> Restorre</a> |
                                            <form id="delete-form-{{$i}}" method="POST" action="{{route('transfer-orders.destroy' , encrypt($order->id))}}" style="display: inline;">
                                                @csrf         
                                                @method('DELETE')
                                                <a href="#" class="button" onclick="confirmDelete('{{$i}}')" style="color: red;"><i class="fa fa-trash"></i> Delete</a>
                                            </form> @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>                        
                            </table>
                        </div>
                        <div class="tab-pane fade table-responsive" id="tab_2" role="tabpanel">
                            <table id="sto-returns" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>{{trans('navmenu.date')}}</th>
                                        <th>{{trans('navmenu.sto_no')}}</th>
                                        <th>{{trans('navmenu.source_shop')}}</th>
                                        <th>{{trans('navmenu.destin_shop')}}</th>
                                        <th>Requested By</th>
                                        <th>{{trans('navmenu.transfer_by')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.transfer_type')}}</th>
                                        <th>Request Time</th>
                                        <th>Confirm Time</th>
                                        <th>Receive Time</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sto_returns as $i => $order)
                                    <tr>
                                        <td>{{$order->order_date}}</td>
                                        <td> <a href="{{route('transfer-orders.show', encrypt($order->id))}}"> {{ sprintf('%04d', $order->order_no)}}</a></td>
                                        <td>{{$shop->name}}</td>
                                        <td>{{\App\Models\Shop::find($order->destination_id)->name}}</td>
                                        <td>
                                            @if(!is_null($order->requester_id))
                                            {{App\Models\User::find($order->requester_id)->first_name}}
                                            @else
                                            
                                            @endif
                                        </td>
                                        <td>
                                            @if(!is_null($order->user_id))
                                            {{App\Models\User::find($order->user_id)->first_name}}
                                            @else

                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            @if($order->is_request)
                                            <span class="badge bg-warning">
                                            Request
                                            </span>
                                            @endif
                                            @if($order->is_return)
                                            <span class="badge bg-secondary">
                                            Return
                                            </span>
                                            @endif
                                            @if(!$order->is_request && !$order->is_return)
                                            <span class="badge bg-success">
                                            Normal
                                            </span>
                                            @endif
                                        </td>
                                        <td>{{$order->created_at}}</td>
                                        <td>{{$order->confirm_time}}</td>
                                        <td>{{$order->receive_time}}</td>
                                        <td>
                                            @if(!$order->is_cancelled && $order->status != 'Received')
                                            <a href="{{route('transfer-orders.edit', encrypt($order->id))}}">
                                                <i class="fa fa-edit" style="color: blue;"></i>
                                             Edit </a> @if(Auth::user()->can('cancel-stock-transfer'))| 
                                            <a href="#" class="text-warning" onclick="confirmCancel('<?php echo encrypt($order->id) ?>')"><i class="fa fa-x-circle"></i> Cancel</a> @endif @endif @if($order->is_cancelled && Auth::user()->can('cancel-stock-transfer')) | 
                                            <a href="#" class="text-success" onclick="confirmCancel('<?php echo encrypt($order->id) ?>')"><i class="fa fa-x-circle"></i> Restorre</a> |
                                            <form id="delete-form-{{$i}}" method="POST" action="{{route('transfer-orders.destroy' , encrypt($order->id))}}" style="display: inline;">
                                                @csrf         
                                                @method('DELETE')
                                                <a href="#" class="button" onclick="confirmDelete('{{$i}}')" style="color: red;"><i class="fa fa-trash"></i> Delete</a>
                                            </form> @endif
                                        </td>
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
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script src="{{ asset('assets/vendor/sweetalert/sweetalert.min.js') }}"></script> <!-- SweetAlert Plugin Js --> 
    
    <script>
        $(function () {
            $('#sto-requests').DataTable();
            $('#sto-returns').DataTable();
        });
    </script>
@endsection