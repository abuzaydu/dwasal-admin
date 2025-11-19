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
                    {{-- create uequivalent card --}}
                    <div class="border rounded p-4">
                        <h6>New  uequivalent</h6>
                        <form class="form row g-1" method="POST" action="{{route('unit-equivalents.update', encrypt($uequivalent->id))}}" id="create">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-sm-4">
                                <label class="form-label">Unit A<span class="text-danger">*</span></label>
                                <select name="unit_a" class="form-select form-select-sm mb-1" required>
                                    <option>Select Unit</option>
                                    @foreach($units as $unit)@if($unit->unit_name == $uequivalent->unit_a)
                                    <option selected>{{$unit->unit_name}}</option>
                                    @else
                                    <option>{{$unit->unit_name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Unit B <span class="text-danger">*</span></label>
                                <select name="unit_b" class="form-select form-select-sm mb-1" required>
                                    <option>Select Unit</option>
                                    @foreach($units as $unit)
                                    @if($unit->unit_name == $uequivalent->unit_b)
                                    <option selected>{{$unit->unit_name}}</option>
                                    @else
                                    <option>{{$unit->unit_name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Unit B Value (Equivalent to 1 Unit of A)<span class="text-danger">*</span></label>
                                <input type="number" min="0", step="any" class="form-control form-control-sm mb-1" name="unit_b_value" value="{{$uequivalent->unit_b_value+0}}" id="weight" placeholder="Please Enter Default Weight" required>
                            </div>
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-success btn-sm">Update</button>
                                <a class="btn btn-warning btn-sm" href="{{ url('unit-equivalents') }}"><i class="fa fa-times-circle"></i> Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection