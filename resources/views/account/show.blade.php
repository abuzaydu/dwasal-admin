@extends('layouts.prof')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-1">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Account & Settings</li>  
                    <li class="breadcrumb-item"><a href="{{ url('shops') }}">My Businesses, Shops & Warehouses</a></li>  
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
 
    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <!-- form start -->
                    <form class="form" action="{{route('shops.update' , encrypt($shop->id))}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row g-1">
                            <h6 class="card-title">Shop/Business Details</h6>
                            <hr>
                            <div class="col-sm-6">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="name" name="shop_name" value="{{$shop->name}}" placeholder="Business name" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="name" class="form-label">Short Description</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="short_desc" name="short_desc" value="{{$shop->short_desc}}" placeholder="Short Description">
                            </div>
                            <div class="col-sm-3">
                                <label for="tin" class="form-label">TIN</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="tin" name="tin" value="{{$shop->tin}}" data-inputmask='"mask": "999-999-999"' data-mask placeholder="Optional(TIN)">
                            </div>
                            <div class="col-sm-3">
                                <label for="vrn" class="form-label">VRN</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="vrn" name="vrn" value="{{$shop->vrn}}" placeholder="Optional(VRN)" data-inputmask='"mask": "99-999999-A"' data-mask>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">Is Main Branch? <span style="color:red">*</span></label>
                                <select name="is_hq" class="form-select form-select-sm mb-1">
                                    @if($shop->is_hq)
                                    <option value="1">YES</option>
                                    <option value="0">NO</option>
                                    @else
                                    <option value="0">NO</option>
                                    <option value="1">YES</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">Is A Warehouse Only? <span style="color:red">*</span></label>
                                <select name="is_warehouse" class="form-select form-select-sm mb-1">
                                    @if($shop->is_warehouse)
                                    <option value="1">YES</option>
                                    <option value="0">NO</option>
                                    @else
                                    <option value="0">NO</option>
                                    <option value="1">YES</option>
                                    @endif
                                </select>
                            </div>

                            <h6 class="card-title mt-3">Contact information</h6>
                            <hr>
                            <div class="col-sm-3">
                                <label for="phone" class="form-label">Telephone</label>
                                <input type="tel" class="form-control form-control-sm mb-1" id="tel" name="tel" value="{{$shop->tel}}" placeholder="Telephone number">
                            </div>
                            <div class="col-sm-3">
                                <label for="mobile" class="form-label">Mobile</label>
                                <input type="mobile" class="form-control form-control-sm mb-1" id="mobile" name="mobile" value="{{$shop->mobile}}" placeholder="Mobile number">
                            </div>
                            <div class="col-sm-3">
                                <label for="mobile" class="form-label">WhatsApp</label>
                                <input type="mobile" class="form-control form-control-sm mb-1" id="mobile" name="whatsapp" value="{{$shop->whatsapp}}" placeholder="WhatsApp Number">
                            </div>
                            <div class="col-sm-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control form-control-sm mb-1" id="email" name="email" value="{{$shop->email}}" placeholder="Email Address">
                            </div>
                            <div class="col-sm-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="website" class="form-control form-control-sm mb-1" id="website" name="website" value="{{$shop->website}}" placeholder="Email Address">
                            </div>
                            <div class="col-sm-3">
                                <label for="street" class="form-label">Postal Address</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="street" name="postal_address" value="{{$shop->postal_address}}" placeholder="Postal Address">
                            </div>
                            <div class="col-sm-3">
                                <label for="street" class="form-label">Physical Address</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="street" name="physical_address" value="{{$shop->physical_address}}" placeholder="Physical Address">
                            </div>
                            <div class="col-sm-3">
                                <label for="street" class="form-label">Street</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="street" name="street" value="{{$shop->street}}" placeholder="Street">
                            </div>
                            <div class="col-sm-3">
                                <label for="district" class="form-label">District</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="district" name="district" value="{{$shop->district}}" placeholder="District">
                            </div>
                            <div class="col-sm-3">
                                <label for="city" class="form-label">Town / City</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="city" name="city" value="{{$shop->city}}" placeholder="Town or City">
                            </div>
                            <div class="col-sm-3">
                                <label for="city" class="form-label">Country</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="city" name="country" value="{{$shop->country}}" placeholder="Country">
                            </div>
                            
                            <h6 class="card-title mt-3">Shop / Business Logo & Stamp</h6>
                            <hr>
                            <div class="col-sm-6">
                                <label class="form-label">Please upload your logo here</label>
                                <input type="file" id="exampleInputFile" name="image">

                                <div class="row align-items-center">
                                    @if(!is_null($shop->logo_location))
                                    <figure>
                                        <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="logo" width="250">
                                    </figure>
                                    @endif
                                </div>                            
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Please upload your Stamp here</label>
                                <input type="file" id="exampleInputFile" name="stamp">
                                <div class="row align-items-center">
                                    @if(!is_null($shop->stamp))
                                    <figure>
                                      <img class="invoice-logo" src="{{asset('storage/stamps/'.$shop->stamp)}}" alt="Stamp" width="100" height="90">
                                    </figure>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-info btn-sm float-end">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
          <!-- /.card -->
        </div>
    </div>
@endsection