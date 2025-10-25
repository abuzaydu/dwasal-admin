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

            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    {{-- create drate card --}}
                    <div class="border rounded p-4">
                        <form class="form row g-1" method="POST" action="{{route('delivery-rates.update', encrypt($drate->id))}}" id="create">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-sm-4">
                                <label class="form-label">Distance in Km<span class="text-danger">*</span></label>
                                <input type="number" class="form-control form-control-sm mb-1" name="distance" id="distance" value="{{$drate->distance}}" placeholder="Please Enter Default Distance" required>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Weight in Kg <span class="text-danger">*</span></label>
                                <input type="number" class="form-control form-control-sm mb-1" name="weight" id="weight" value="{{$drate->weight}}" placeholder="Please Enter Default Weight" required>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Rate Amount Tsh <span class="text-danger">*</span></label>
                                <input type="number" min="0" step="any" name="rate_amount" id="rate-amount" value="{{$drate->rate_amount}}" class="form-control form-control-sm mb-1" placeholder="Enter Details" required>
                            </div>
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-success btn-sm">Update</button>
                                <a class="btn btn-warning btn-sm" href="{{ url('delivery-rates') }}"><i class="fa fa-times-circle"></i> Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection