@extends('layouts.vms')

@section('content')
    <div class="block-header pt-4">
        <div class="row align-items-center">
            <div class="col-lg-8 col-md-8 col-sm-12 mb-2">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('maintenance') }}">Maintenance</a></li>
                    <li class="breadcrumb-item active">{{ $page }}</li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 mb-2 text-end">
                <a href="{{ route('maintenance-types.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus me-1"></i> Add Type
                </a>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-xl-12 mx-auto">
            <div class="card radius-6">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="maintenance-types-table" class="table table-striped display nowrap datatable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($maintenanceTypes as $type)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $type->type }}</td>
                                        <td>
                                            @if($type->active)
                                                <span class="badge rounded-pill bg-success">Active</span>
                                            @else
                                                <span class="badge rounded-pill bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="{{ route('maintenance-types.edit', encrypt($type->id)) }}" class="text-info me-2" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('maintenance-types.destroy', encrypt($type->id)) }}" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <a href="javascript:;" onclick="if(confirm('Deactivate this maintenance type?')) { this.closest('form').submit(); }" class="text-danger" title="Deactivate">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($maintenanceTypes->isEmpty())
                        <div class="alert alert-light mt-3 mb-0 py-2" role="alert">
                            No maintenance types found. Add the first type to enable vehicle maintenance creation.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script>
        $(document).ready(function(){
            $('#maintenance-types-table').DataTable({
                paging: true,
                ordering: true,
                searching: true,
                responsive: true,
                autoWidth: false
            });
        });
    </script>
@endsection

