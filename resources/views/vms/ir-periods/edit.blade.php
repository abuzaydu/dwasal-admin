@extends('layouts.vms')
@section('content')
<div class="block-header pt-4">
    <div class="row">
        <div class="col-lg-6">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('vehicles-dash') }}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item">Vehicle Management</li>
                <li class="breadcrumb-item"><a href="{{ route('ir-periods.index') }}">IR Periods</a></li>
                <li class="breadcrumb-item active">{{ $page }}</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card radius-6">
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('ir-periods.update', encrypt($period->id)) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Period</label>
                        <input type="text" name="period" class="form-control form-control-sm" value="{{ old('period', $period->period) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control form-control-sm" value="{{ old('description', $period->description) }}">
                    </div>

                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" name="active" value="1" id="active_period" {{ $period->active ? 'checked' : '' }}>
                        <label class="form-check-label" for="active_period">Active</label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    <a href="{{ route('ir-periods.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

