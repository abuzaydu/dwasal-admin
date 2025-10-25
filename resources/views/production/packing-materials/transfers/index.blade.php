@extends('layouts.prod')
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

    function confirmCancel(id) {
        Swal.fire({
          title: "Are you sure you want to Cancel this Order",
          text: "This will restore all stocks affected",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes Cancel",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href = "{{ url('cancel-sto') }}/"+id;
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
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
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item"><a href="{{url('packing-materials')}}">Packing Materials</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right pt-0">
                <form class="dashform" action="{{ url('f-pm-transfers') }}" method="POST" id="stockform">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="col-md-12">
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
                    <ul class="nav nav-tabs nav-tabs-new2" role="tablist"  >
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_1" role="tab"><i class='fa fa-list-plus font-18 me-1'></i> Outgoing PM Transfers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_2" role="tab"><i class='fa fa-list-plus font-18 me-1'></i> INcoming PM Transfers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"  href="{{route('pm-transfers.create')}}"><i class='fa fa-plus-circle font-18 me-1'></i> New PM Transfer</a>
                        </li>
                    </ul>
                    <div class="tab-content py-1">
        				<div class="tab-pane fade show active table-responsive" id="tab_1" role="tabpanel">
                            <table id="sto-requests" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>{{trans('navmenu.date')}}</th>
                                        <th>PMT No.</th>
                                        <th>{{trans('navmenu.source_shop')}}</th>
                                        <th>{{trans('navmenu.destin_shop')}}</th>
                                        <th>{{trans('navmenu.transfer_by')}}</th>
                                        <th>Received By</th>
                                        <th>{{trans('navmenu.status')}}</th>
                                        <th>{{trans('navmenu.created_at')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transfers as $i => $order)
                                    <?php $destin = App\Models\Shop::find($order->destin_id); ?>
                                    <tr>
                                        <td>{{$order->pm_transfer_date}}</td>
                                        <td style="text-align: center;"> <a href="{{route('pm-transfers.show', encrypt($order->id))}}"> {{ sprintf('%04d', $order->pmt_no)}}</a></td>
                                        <td>{{\App\Models\Shop::find($order->shop_id)->name}}</td>
                                        <td>@if(!is_null($destin)){{$destin->name}}@endif</td>
                                        <td>
                                            @if(!is_null($order->user_id))
                                            {{App\Models\User::find($order->user_id)->first_name}}
                                            @else

                                            @endif
                                        </td>
                                        <td>
                                            {{$order->receiver}}
                                        </td>
                                        <td>{{$order->status}}</td>
                                        <td>{{$order->created_at}}</td>
                                        <td>
                                            @if($order->status != 'Received')
                                            <a href="{{route('transfer-orders.edit', encrypt($order->id))}}">
                                                <i class="fa fa-edit" style="color: blue;"></i>
                                             Edit |
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
                            <table id="inc-transfers" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>{{trans('navmenu.date')}}</th>
                                        <th>PMT No.</th>
                                        <th>{{trans('navmenu.source_shop')}}</th>
                                        <th>{{trans('navmenu.destin_shop')}}</th>
                                        <th>{{trans('navmenu.transfer_by')}}</th>
                                        <th>Received By</th>
                                        <th>{{trans('navmenu.status')}}</th>
                                        <th>{{trans('navmenu.created_at')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inc_transfers as $i => $order)
                                    <?php $destin = App\Models\Shop::find($order->destin_id); ?>
                                    <tr>
                                        <td>{{$order->pm_transfer_date}}</td>
                                        <td style="text-align: center;"> <a href="{{route('pm-transfers.show', encrypt($order->id))}}"> {{ sprintf('%04d', $order->pmt_no)}}</a></td>
                                        <td>{{\App\Models\Shop::find($order->shop_id)->name}}</td>
                                        <td>@if(!is_null($destin)){{$destin->name}}@endif</td>
                                        <td>
                                            @if(!is_null($order->user_id))
                                            {{App\Models\User::find($order->user_id)->first_name}}
                                            @else

                                            @endif
                                        </td>
                                        <td>
                                            {{$order->receiver}}
                                        </td>
                                        <td>{{$order->status}}</td>
                                        <td>{{$order->created_at}}</td>
                                        <td>
                                            @if($order->status != 'Received')
                                            <a href="{{route('transfer-orders.edit', encrypt($order->id))}}">
                                                <i class="fa fa-edit" style="color: blue;"></i>
                                             Edit |
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
            $('#sto-requests').DataTable({
                'order': [[1, 'desc']]
            });
            $('#inc-transfers').DataTable({
                'order': [[1, 'desc']]
            });
        });
    </script>
@endsection