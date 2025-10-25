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
                    <ul class="nav nav-tabs nav-success" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="pill" href="#tab_1-0" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-check font-18 me-1'></i></div>
                                    <div class="tab-title">Businees Types</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="pill" href="#tab_1-1" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-plus font-18 me-1'></i></div>
                                    <div class="tab-title">Sub Types</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active table-responsive" id="tab_1-0" role="tabpanel">
                            <table id="example1" class="table table-responsive table-striped" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image/Icon</th>
                                        <th>Business Type</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($types as $key => $type)
                                    <tr>
                                        <td>{{ $key+1  }}</td>
                                        <td><img class="invoice-logo" src="{{asset('storage/btypes/'.$type->type_icon)}}" alt="" width="70"></td>
                                        <td>{{ $type->type }}</td>
                                        <td>{{ $type->description }} </td>
                                        <td>
                                            <a  href="{{  route('types.edit', encrypt($type->id)) }}">
                                                <i class="fa fa-edit"></i>
                                            </a> |
                                            <a href="{{ url('admin/types/destroy', $type->id) }}" onclick="return confirm('Are you sure you want to delete this record?.')">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade table-responsive" id="tab_1-1" role="tabpanel">
                            
                        </div>
                        <div class="tab-pane fade" id="tab_1-2">
                            <div class="px-2 pt-2 rounded">
                                <form class="form row g-3" method="post" action="{{ route('types.store') }}" validate>
                                    {{csrf_field()}}
                                    <div class="col-md-3">
                                        <label class="form-label">Parent Type</label>
                                        <select name="business_type_id" id="btype" class="form-select form-select-sm mb-1" required>
                                            <option value="">{{trans('navmenu.select_business_type')}}</option>
                                            @foreach($types as $key => $type)
                                            <option value="{{$type->id}}">{{$type->id}}. {{$type->type}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Business Type</label>
                                        <input class="form-control form-control-sm mb-1 border-primary" type="text" name="name" placeholder="Enter Business Type" id="userinput8" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" rows="1" class="form-control form-control-sm mb-1" placeholder="Please Enter type- description"></textarea>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Business Type in Swahili</label>
                                        <input class="form-control form-control-sm mb-1 border-primary" type="text" name="name_sw" placeholder="Enter Business Type" id="userinput8">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Description  in Swahili</label>
                                        <textarea name="description_sw" rows="1" class="form-control form-control-sm mb-1" placeholder="Please Enter type- description"></textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                        <button type="reset" class="btn btn-warning btn-sm">Reset</button>
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