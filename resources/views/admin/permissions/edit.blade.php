@extends('layouts.adm')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Accounts & Users</li>       
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
                    <form class="form" method="post" action="{{ route('permissions.update', encrypt($permission->id)) }}" validate>
                        @csrf
                        @method('PUT')
                        <div class="form-body row">
                            <div class="col-md-3">
                                <label class="form-label">Feature <span style="color: red;"></span></label>
                                <select name="feature_id" class="form-select form-select-sm mb-1" required>
                                    <option value="">Select Feature</option>
                                    @foreach($features as $f)
                                    <option value="{{$f->id}}">{{$f->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="userinput8">Permission Name</label>
                                <input value="{{ $permission->name }}" class="form-control form-control-sm mb-1 border-primary" type="text" name="name" placeholder="Enter Permission Name" id="userinput8" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="userinput8">Permission Display Name</label>
                                <input value="{{ $permission->display_name }}" class="form-control form-control-sm mb-1 border-primary" type="text" name="display_name" placeholder="Enter Permission Display Name" id="userinput8" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="userinput8">Permission Description</label>
                                <textarea name="description" rows="1" class="form-control form-control-sm mb-1" placeholder="Enter permission description">{{ $permission->description }}</textarea>
                            </div>
                            <div class="form-actions pt-3 right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-check2"></i> Save
                                </button>
                                <a href="{{ url('admin/permissions') }}" class="btn btn-warning">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
