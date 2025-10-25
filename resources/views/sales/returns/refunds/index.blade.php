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
</script>

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item"><a href="{{ url('sales-returns') }}">Sales Returns</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
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
                                            <th>Date</th>
                                            <th>Customer</th>
                                            <th>Ref. No</th>
                                            <th>Amount</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <!-- <th>Created By</th>
                                            <th>Approved By</th>
                                            <th>Approved At</th>
                                            <th>Refunded By</th>
                                            <th>Refunded At</th> -->
                                            <th>Last updated</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($refunds as $index => $refund)
                                        <tr>
                                            <td>{{$index+1}}</td></td>
                                            <td>{{date('d-m-Y', strtotime($refund->created_at))}}</td>
                                            <td><a href="{{ route('refund-requests.show', encrypt($refund->id)) }}">{{$refund->name}}</a></td>
                                            <td style="text-align: center;">{{ sprintf('%04d', $refund->refund_no)}}</td>
                                            <td>{{number_format($refund->refund_amt, 2, '.', ',')}}</td>
                                            <td>{{date('d-m-Y H:i', strtotime($refund->created_at))}}</td>
                                            <td>{{$refund->status}}</td>
                                            <!-- <td>{{$refund->first_name}}</td>
                                            <td>{{$refund->approved_by}}</td>
                                            <td>@if(!is_null($refund->approved_time)){{date('d-m-Y H:i', strtotime($refund->approved_time))}}@endif</td>
                                            <td>{{$refund->confirmed_by}}</td>
                                            <td>@if(!is_null($refund->confirm_time)){{date('d-m-Y H:i', strtotime($refund->confirm_time))}}@endif</td> -->
                                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $refund->updated_at)->diffForHumans() }}</td>
                                            @if($refund->status == 'Paid')
                                            <td></td>
                                            @else
                                            <td>
                                                @if(is_null($refund->approved_time))
                                                <a href="{{ route('refund-requests.edit', encrypt($refund->id)) }}"><i class="fa fa-edit" style="color: blue;"></i></a> |@endif
                                                <form id="delete-form-{{$index}}" method="POST" action="{{ route('refund-requests.destroy', encrypt($refund->id))}}" style="display: inline;">
                                                    @csrf
                                                    @method("DELETE")
                                                    <a href="#" onclick="confirmDelete('<?php echo $index; ?>')"><i class="fa fa-trash" style="color: red;"></i></a>
                                                </form>
                                            </td>
                                            @endif
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