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
            alert(id)
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
                    <li class="breadcrumb-item"><a href="{{ url('an-sales') }}">Invoices</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-header">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped display nowrap" style="width: 100%;">
                            <thead style="font-weight: bold; font-size: 14;">
                                <tr>
                                    <th>#</th>
                                    <th>Invoice No.</th>
                                    <th>Credit Note No.</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Created At</th>
                                    <th>Last updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cnotes as $index => $cnote)
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td> {{ sprintf('%04d', $cnote->invoice_no)}}</td>
                                    <td><a href="{{ route('credit-notes.show', encrypt($cnote->id)) }}"> {{ sprintf('%04d', $cnote->credit_note_no)}}</a></td>
                                    <td>{{$cnote->name}}</td>
                                    <td>{{number_format($cnote->amount)}}</td>
                                    <td>{{$cnote->due_date}}</td>
                                    <td>{{$cnote->created_at}}</td>
                                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $cnote->updated_at)->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('credit-notes.edit', encrypt($cnote->id)) }}"><i class="fa fa-edit" style="color: blue;"></i></a> |
                                        <form id="delete-form-{{$index}}" method="POST" action="{{ route('credit-notes.destroy', encrypt($cnote->id))}}" style="display: inline;">
                                            @csrf
                                            @method("DELETE")
                                            <a href="#" onclick="confirmDelete('<?php echo $index; ?>')"><i class="fa fa-trash" style="color: red;"></i></a>
                                        </form>
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
        });
    </script>
@endsection