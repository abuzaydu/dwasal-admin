@extends('layouts.inv')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script type="text/javascript">
    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure')}}",
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
</script>

@section('content')
    
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                <form class="dashform row g-3" action="{{ url('f-trip-logs')}}" method="POST">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="col-sm-7">
                        <div class="input-group">
                            <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                                <span><i class="fa fa-calendar"></i></span>
                                <i class="fa fa-caret-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <a href="{{ route('trip-logs.create')}}" class="btn btn-primary btn-sm" onclick="showHideForm('show')" style="margin: 5px;"><i class="bx bxs-plus-square"></i>New Trip</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto mt-0">
            <div class="card">
                 <div class="card-body">
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="trip-logs" role="tabpanel">
                            <div class="table-responsive">
                                <table id="del-multiple" class="table table-striped table-bordered display nowrap">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Vehicle</th>
                                            <th>Began</th>
                                            <th>Ended</th>
                                            <th>from</th>
                                            <th>To</th>
                                            <!-- <th>Start</th> -->
                                            <!-- <th>Finish</th> -->
                                            <!-- <th>Dist. Traveled</th> -->
                                            <!-- <th>Fuel(Ltrs)</th> -->
                                            <!-- <th>Fuel Cost</th> -->
                                            <th>Trip Description</th>
                                            <th>Entry Date</th>
                                            <th>Entry Person</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($triplogs as $key => $trip)

                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$trip->device_number}} - {{$trip->device_name}}</td>
                                            <td>{{ date('d/m/Y H:i', strtotime($trip->trip_date))}}</td>
                                            <td>{{ date('d/m/Y H:i', strtotime($trip->trip_end_date))}}</td>
                                            <td>{{$trip->from}}</td>
                                            <td>{{$trip->to }}</td>
                                            <!-- <td>{{$trip->mileage_out+0}}</td>  -->
                                            <!-- <td>{{$trip->mileage_in+0}}</td>  -->
                                            <!-- <td>{{$trip->mileage_in-$trip->mileage_out}}</td>   -->
                                            <!-- <td>{{$trip->fuel+0}}</td> -->
                                            <!-- <td>{{ number_format($trip->fuel*$trip->fuel_unit_cost, 2, '.', ',') }}</td>  -->
                                            <td>{{$trip->trip_title}}</td>
                                            <td>{{ date('d/m/Y H:i:s', strtotime($trip->created_at))}}</td>
                                            <td>{{$trip->first_name}} {{$trip->last_name}}</td>
                                            <td>
                                                <a href="{{ route('trip-logs.show', encrypt($trip->id)) }}"><i class="fa fa-eye"></i> View Trip Details</a> @if(Auth::user()->can('edit-trip-log'))| 
                                                <a href="{{ route('trip-logs.edit', encrypt($trip->id))}}">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                                </a>@endif  @if(Auth::user()->can('delete-trip-log'))|
                                                <form id="delete-form-{{$key}}" action="{{route('trip-logs.destroy' , encrypt($trip->id) )}}" method="POST" style="display: inline-block;">
                                                    @method('DELETE')
                                                     @csrf
                                                    <a href="#" class="button" onclick="confirmDelete('{{$key}}')"><i class="fa fa-trash" style="color: red;"></i></a>
                                                </form>@endif
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
    </div>
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    
    <script>
        $(function () {
            //Exportable table
            $('#del-multiple').DataTable();            
        });
    </script>
@endsection