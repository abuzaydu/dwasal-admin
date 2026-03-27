@extends('layouts.vms')
@section('content')
<div class="block-header pt-4">
    <div class="row">
        <div class="col-lg-6">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('vehicles-dash') }}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item">Vehicle Management</li>
                <li class="breadcrumb-item"><a href="{{ url('insurance') }}">Insurance</a></li>
                <li class="breadcrumb-item active">{{ $page }}</li>
            </ul>
        </div>
    </div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<div class="row">
    <div class="col-12">
        <div class="card radius-6">
            <div class="card-body">
                <form method="POST" action="{{ route('insurance-companies.store') }}" class="row g-2 mb-3">
                    @csrf
                    <div class="col-md-4">
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="Company name" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="description" class="form-control form-control-sm" placeholder="Description (optional)">
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="active" value="1" checked id="active_new_company">
                            <label class="form-check-label" for="active_new_company">Active</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">Add</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Active</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($companies as $c)
                                <tr>
                                    <td>{{ $c->name }}</td>
                                    <td>{{ $c->description }}</td>
                                    <td>
                                        @if($c->active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td style="text-align:right;">
                                        <a href="{{ route('insurance-companies.edit', encrypt($c->id)) }}" class="btn btn-xs btn-outline-secondary">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('insurance-companies.destroy', encrypt($c->id)) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this insurance company?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No insurance companies.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $companies->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

