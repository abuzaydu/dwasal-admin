@extends('layouts.adm')
<script type="text/javascript">
    function confirmDelete(id) {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                document.getElementById('delete-form-' + id).submit();
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
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
                    <li class="breadcrumb-item">Service Payments</li>       
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="pill" href="#tab_1-0" role="tab" aria-selected="true"><i class='fa fa-list-plus font-18 me-1'></i>Service Charges</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="pill" href="#tab_1-1" role="tab" aria-selected="true"><i class='fa fa-list-plus font-18 me-1'></i>New Service Charge</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url('admin/modules') }}">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-check font-18 me-1'></i></div>
                                    <div class="tab-title">Modules</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab_1-0">
                            <table id="example1" class="table table-striped display nowrap" style="width: 100%;">
                                <thead style="font-weight: bold; font-size: 14;">
                                    <tr>
                                        <th style="width: 10px;">#</th>
                                        <th>Package</th>
                                        <th>Pricet</th>
                                        <th>Duration</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($service_charges as $key => $charge)
                                    <tr>
                                        <td>{{ $key+1  }}</td>
                                        <td>{{ $charge->title }} </td>
                                        <td>{{ $charge->initial_pay }}</td>
                                        <td>{{ $charge->duration}} </td>
                                        <td>
                                            <a  href="{{  route('service-charges.edit', encrypt($charge->id)) }}">
                                                <i class="fa fa-edit"></i>
                                            </a> |
                                            <form id="delete-form-{{ $key }}" method="POST" action="{{ route('service-charges.destroy', encrypt($charge->id)) }}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" class="button" onclick="confirmDelete('{{ $key }}')"><i class="fa fa-trash" style="color: red;"></i></a>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="tab_1-1">
                            <div class="p-2 rounded">
                                <form class="form row g-1" method="POST" action="{{ route('service-charges.store') }}" validate>
                                    <h6 class="mb-2 mt-2 text-uppercase text-center">New Service Charge</h6>
                                    {{csrf_field()}}
                                    <div class="col-md-3">
                                        <label class="form-label">Package <span style="color:red;">*</span></label>
                                        <select class="form-select form-select-sm mb-1 border-primary+" id="userinput6" name="type" required>
                                            @foreach($subscriptions as $type)
                                            <option value="{{$type->id}}">{{$type->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Amount <span style="color:red;">*</span></label>
                                        <input class="form-control form-control-sm mb-1 border-primary" type="number" name="initial_pay" placeholder="Enter Initial Payment amount" id="userinput8" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="form-label">Duration <span style="color:red;">*</span></label>
                                        <select class="form-select form-select-sm mb-1 border-primary+" id="userinput6" name="duration" required>
                                            <option value="">Select Duration</option>
                                            <option>Monthly</option>
                                            <option>Quarterly</option>
                                            <option>Semi Annually</option>
                                            <option>Annually</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="{{ url('admin/service-charges') }}" class="btn btn-warning btn-sm">Cancel</a>
                                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection