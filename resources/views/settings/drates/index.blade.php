@extends('layouts.prof')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
    <script type="text/javascript">
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure, You want to delete this record?',
                showDenyButton: true,
                confirmButtonText: 'Yes Delete',
                denyButtonText: `Don't Delete`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                    Swal.fire('Deleted!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('Record not deleted', '', 'info')
                }
            })
        }
    </script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header mb-1">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('home') }}"><i class="icon-home"></i></a></li>                            
                    <li class="breadcrumb-item">Site Settings</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>           
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="$('#create_drate').show('fast');">New Delivery Rate
                </button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    {{-- create drate card --}}
                    <div class="border rounded p-4" id="create_drate" style='display:none;'>
                        <h6>New  drate</h6>
                        <form class="form row g-1" method="POST" action="{{route('delivery-rates.store')}}" id="create">
                            @csrf
                            <div class="col-sm-4">
                                <label class="form-label">Distance in Km<span class="text-danger">*</span></label>
                                <input type="number" class="form-control form-control-sm mb-1" name="distance" id="distance" placeholder="Please Enter Default Distance" required>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Weight in Kg <span class="text-danger">*</span></label>
                                <input type="number" class="form-control form-control-sm mb-1" name="weight" id="weight" placeholder="Please Enter Default Weight" required>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Rate Amount Tsh <span class="text-danger">*</span></label>
                                <input type="number" min="0" step="any" name="rate_amount" id="rate-amount" class="form-control form-control-sm mb-1" placeholder="Enter Details" required>
                            </div>
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-success btn-sm">Add</button>
                                <a class="btn btn-warning btn-sm" onclick="$('#create_drate').hide('fast'); document.getElementById('create').reset(); "><i class="fa fa-times-circle"></i> Cancel</a>
                            </div>
                        </form>
                    </div>
                    <div class="item-list">
                        @include('flash-message')
                        <table id="drates" class="table table-hover display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Distance Km</th>
                                    <th>Weight Kg</th>
                                    <th>Rate</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($drates as $key => $drate)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$drate->distance+0}}</td>
                                    <td>{{$drate->weight+0}}</td>
                                    <td>{{$drate->rate_amount}}</td>
                                    <td style="white-space: nowrap;">
                                        <a href="{{ route('delivery-rates.edit', encrypt($drate->id))}}"><i class="fa fa-edit"></i></a>| 
                                        <form id="delete-form-{{$key}}" method="POST" action="{{ route('delivery-rates.destroy', encrypt($drate->id))}}" style="display: inline;"> 
                                            @csrf
                                            @method("DELETE")
                                            <a href="javascript:;" class="text-danger" onclick=" return confirmDelete('<?php echo $key; ?>')">
                                                <i class='fa fa-trash mr-1'></i></a>
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
    <!--end row-->
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    
    <script>
        $(function () {
            //Exportable table
            $('#drates').DataTable({
                'scrollX': true
            });
        });
    </script>
@endsection