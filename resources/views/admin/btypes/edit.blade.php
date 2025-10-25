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
                    <div class="px-2 pt-2 rounded">
                        <form class="form row g-3" method="post" action="{{ route('types.update', encrypt($btype->id)) }}" validate enctype="multipart/form-data">
                            {{csrf_field()}}
                            {{ method_field('PATCH') }}
                            @if(!is_null($btype->type_icon))
                            <div class="col-md-3">
                                <figure>
                                    <img class="invoice-logo" src="{{asset('storage/btypes/'.$btype->type_icon)}}" alt="" width="70">
                                </figure>
                            </div>
                            @endif
                            <div class="col-md-3">
                                <label for="name" class="form-label">Type Icon</label>
                                <input type="file" id="exampleInputFile" name="image">
                                <p class="help-block">Please upload here.</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Business Type</label>
                                <input class="form-control form-control-sm mb-1 border-primary" type="text" name="type" value="{{$btype->type}}" placeholder="Enter Business Type" id="userinput8" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Description</label>
                                <textarea name="description" rows="1" class="form-control form-control-sm mb-1" placeholder="Please Enter type- description">{{$btype->description}}</textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Business Type in Swahili</label>
                                <input class="form-control form-control-sm mb-1 border-primary" type="text" name="type_sw" value="{{$btype->type_sw}}" placeholder="Enter Business Type" id="userinput8">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Description  in Swahili</label>
                                <textarea name="description_sw" rows="1" class="form-control form-control-sm mb-1" placeholder="Please Enter type- description">{{$btype->description_sw}}</textarea>
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
@endsection