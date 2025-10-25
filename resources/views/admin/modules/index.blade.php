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
                            <a class="nav-link active" data-bs-toggle="pill" href="#tab_1-0" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-check font-18 me-1'></i></div>
                                    <div class="tab-title">Modules</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="pill" href="#tab_1-1" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-check font-18 me-1'></i></div>
                                    <div class="tab-title">New Modules</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url('admin/service-charges') }}">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-plus font-18 me-1'></i></div>
                                    <div class="tab-title">Service Charges</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active table-responsive" id="tab_1-0" role="tabpanel">
                            <table id="example1" class="table table-striped display nowrap" style="width: 100%;">
                                <thead style="font-weight: bold; font-size: 14;">
                                    <tr>
                                        <th style="width: 10px;">#</th>
                                        <th>Name</th>
                                        <th>Display Name</th>
                                        <th>Price</th>
                                        <th>Duration</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($modules as $key => $charge)
                                    <tr>
                                        <td>{{ $key+1  }}</td>
                                        <td>{{ $charge->name }}</td>
                                        <td>{{$charge->display_name}}</td>
                                        <td>{{ $charge->price }} </td>
                                        <td>{{ $charge->duration}} </td>
                                        <td>{{ $charge->description }}</td>
                                        <td>
                                            <a  href="{{  route('modules.edit', encrypt($charge->id)) }}">
                                                <i class="fa fa-edit"></i>
                                            </a> |
                                            <form id="delete-form-{{ $key }}" method="POST" action="{{ route('modules.destroy', encrypt($charge->id)) }}" style="display: inline;">
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
                        <!-- /.tab-pane -->
                        <div class="tab-pane fade" id="tab_1-1" role="tabpanel">
                            <div class="p-2 rounded">
                                <form class="form row g-3" method="post" action="{{ route('modules.store') }}" validate>
                                    {{csrf_field()}}
                                    <div class="col-md-4">
                                        <label class="form-label">Name <span style="color: red;">*</span> </label>
                                        <input class="form-control form-control-sm mb-1 border-primary" type="text" name="name" placeholder="Enter Module" id="userinput8" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Display Name <span style="color: red;">*</span> </label>
                                        <input class="form-control form-control-sm mb-1 border-primary" type="text" name="display_name" placeholder="Enter Display Name" id="userinput8" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Price <span style="color: red;">*</span> </label>
                                        <input class="form-control form-control-sm mb-1 border-primary" type="number" step="any" name="price" placeholder="Enter Module Price" id="userinput8" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control form-control-sm mb-1 border-primary"></textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <a href="{{ url('admin/modules') }}" class="btn btn-warning btn-sm"> Cancel</a>
                                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection