@extends('layouts.app')
@section('page-styles')
    <link href="{{ asset('side/assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('side/assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script>
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
          title: "Are you sure you want to cancel this booking",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes, cancel It",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href = "{{ url('cancel-booking') }}/"+id;
            Swal.fire(
              "cancelled",
              "cancelled",
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
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right">
                <form class="dashform row g-1" action="{{ url('f-bookings') }}" method="POST" id="stockform">
                    @csrf
                    <div class="col-md-5">
                        @if(Auth::user()->can('create-booking'))
                        <a  class="btn btn-success btn-sm" href="{{route('bookings.create')}}"><i class="fa fa-edit"></i> New Booking</a>
                        @endif
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="col-md-7">
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

    <div class="row clearfix">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="manage-returns" role="tabpanel">
                            <div class="table-responsive">
                                <table id="example" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th>#</th>
                                            <th>Booking ID</th>
                                            <th>Customer</th>
                                            <th>Booking Type</th>
                                            <th>Check In Date</th>
                                            <th>Check Out Date</th>
                                            <th style="text-align: center;">Total Price ({{$defcurr->code}})</th>
                                            <th>Status</th>
                                            <th>Created by</th>
                                            <th>Created At</th>
                                            <th>Last updated</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bookings as $index => $booking)
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td><a href="{{ route('bookings.show', encrypt($booking->id)) }}">{{ $booking->buid }}</a></td>
                                            <td>{{$booking->name}}</td>
                                            <td>{{$booking->booking_type}}</td>
                                            <td>{{date('d/m/Y', strtotime($booking->check_in_date))}}</td>
                                            <td>{{date('d/m/Y', strtotime($booking->check_out_date))}}</td>
                                            <td style="text-align: center;">{{number_format($booking->total_price, 2, '.', ',')}}</td>
                                            <td>
                                                @if ($booking->status == 'Paid')
                                                <span class="badge rounded-pill bg-success">{{ $booking->status }}</span>
                                                @elseif($booking->status == 'Confirmed')
                                                <span class="badge rounded-pill bg-primary">{{ $booking->status }}</span>
                                                @elseif($booking->status == 'Tentative')
                                                <span class="badge rounded-pill bg-warning text-dark">{{ $booking->status }}</span>
                                                @elseif($booking->status == 'Partially Paid')
                                                <span class="badge rounded-pill text-dark" style="background-color: gray;">{{ $booking->status }}</span>
                                                @else
                                                <span class="badge rounded-pill bg-danger">{{ $booking->status }}</span>
                                                @endif</td>
                                            <td>{{ $booking->first_name }}</td>
                                            <td>{{$booking->created_at}}</td>
                                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $booking->updated_at)->diffForHumans() }}</td>
                                            <td style="text-align: center;">
                                                @if(!$booking->is_deleted && Auth::user()->can('edit-booking'))
                                                <a href="{{ route('bookings.edit', encrypt($booking->id)) }}"><i class="fa fa-edit" style="color: blue;"></i></a> |@endif
                                                @if(!$booking->is_deleted && Auth::user()->can('cancel-booking'))
                                                <a href="#" onclick="confirmCancel('<?php echo encrypt($booking->id); ?>')"><i class="fa fa-close" style="color: orange;"></i></a> |@endif
                                                @if($booking->is_deleted && Auth::user()->can('delete-booking'))
                                                <form id="delete-form-{{$index}}" method="POST" action="{{ route('bookings.destroy', encrypt($booking->id))}}" style="display: inline;">
                                                    @csrf
                                                    @method("DELETE")
                                                    <a href="#" onclick="confirmDelete('<?php echo $index; ?>')"><i class="fa fa-trash" style="color: red;"></i></a>
                                                </form>
                                                @endif
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
    <script src="{{ asset('side/assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script>
        $(function () {
            $('#example').DataTable();
            $('#creditsales').DataTable();
        });
    </script>
@endsection


    <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">

    <script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $max = document.querySelector('[name="sale_date"]');
            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                // minDate    : new Date(),
                maxDate    : new Date()
            });
        });
    </script>