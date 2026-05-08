@extends('layouts.prof')
<script>
    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('delete-form-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function showHideForm(elem) {
        var newform = document.getElementById('new-form');
        var newbtn = document.getElementById('new-btn');
        var itemlist = document.getElementById('item-list');
        var newtitle = document.getElementById('new-title');
        var listtitle = document.getElementById('list-title');
        if (elem == 'show') {
            newform.style.display = 'block';
            newtitle.style.display = 'block';
            newbtn.style.display = 'none';
            itemlist.style.display = 'none';
            listtitle.style.display = 'none';
        }else{
            newform.style.display = 'none';
            newtitle.style.display = 'none';
            newbtn.style.display = 'block';
            itemlist.style.display = 'block';
            listtitle.style.display = 'block';
        }
    }

</script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>               
                    <li class="breadcrumb-item">Account & Settings</li>             
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                <button type="button" id="new-btn" class="btn btn-primary btn-sm" onclick="showHideForm('show')"><i class="fa fa-edit"></i> Update Company Details</button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row g-3 clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded" id="new-form" style="display: none;">
                        <form class="form row g-1" method="POST" action="{{route('user-companies.update', encrypt($company->id))}}" enctype="multipart/form-data">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-4">
                                <label class="form-label">Company Name<span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="name" value="{{$company->name}}" required placeholder="Enter Your Company Name" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Company Slogan</label>
                                <input id="name" type="text" name="slogan" value="{{$company->slogan}}" placeholder="Enter Your Company Slogan" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Company Logo</label>
                                <input type="file" name="logo" class="form-control form-control-sm mb-1">
                                @if($company->logo_url)
                                    <img src="{{ asset('storage/clogos/' . $company->logo_url) }}" alt="Current Logo" width="80" class="mt-1">
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Use Invoice Header Banner<span style="color:red">*</span></label>
                                <select name="use_invoice_banner" class="form-select form-select-sm mb-1">
                                    @if($company->use_invoice_banner)
                                    <option value="1">YES</option>
                                    <option value="0">NO</option>
                                    @else
                                    <option value="0">NO</option>
                                    <option value="1">YES</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Company Banner</label>
                                <input type="file" id="banner" name="banner">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Company Stamp</label>
                                <input type="file" id="stamp" name="stamp">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Show Name on ID Card<span style="color:red">*</span></label>
                                <select name="show_name_on_id_card" class="form-select form-select-sm mb-1">
                                    @if($company->show_name_on_id_card)
                                        <option value="1" selected>YES</option>
                                        <option value="0">NO</option>
                                    @else
                                        <option value="0" selected>NO</option>
                                        <option value="1">YES</option>
                                    @endif
                                </select>
                                <small class="text-muted" style="font-size: 0.7rem;">Choose 'No' if your logo already contains the company name.</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Brand Color<span style="color: red; font-weight: bold;">*</span></label>
                                <input value="{{ $company->brand_color }}" type="color" name="brand_color" required class="form-control form-control-sm mb-1">
                            </div>

                            <div class="col-sm-12">
                                <h6 class="card-title mt-3">Contact information</h6>
                                <hr>
                            </div>
                            <div class="col-sm-3">
                                <label for="mobile" class="form-label">Mobile</label>
                                <input type="mobile" class="form-control form-control-sm mb-1" id="mobile" name="mobile" value="{{$company->mobile}}" placeholder="Mobile number">
                            </div>
                            <div class="col-sm-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control form-control-sm mb-1" id="email" name="email" value="{{$company->email}}" placeholder="Email Address">
                            </div>
                            <div class="col-sm-3">
                                <label for="street" class="form-label">Address</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="street" name="address" value="{{$company->address}}" placeholder="Office Address">
                            </div>
                            <div class="col-sm-3">
                                <label for="street" class="form-label">Postal Address</label>
                                <input type="text" class="form-control form-control-sm mb-1" name="postal_code" value="{{$company->postal_code}}" placeholder="Eg P.O.Box XXXXX">
                            </div>
                            <div class="col-sm-3">
                                <label for="city" class="form-label">Town / City</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="city" name="city" value="{{$company->city}}" placeholder="Town or City">
                            </div>
                            <div class="col-sm-3">
                                <label for="city" class="form-label">Country</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="city" name="country" value="{{$company->country}}" placeholder="Country">
                            </div>
                            <div class="col-sm-3">
                                <label for="tin" class="form-label">TIN</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="tin" name="tin" value="{{$company->tin}}" data-inputmask='"mask": "999-999-999"' data-mask placeholder="Company TIN">
                            </div>
                            <div class="col-sm-3">
                                <label for="vrn" class="form-label">VRN</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="vrn" name="vrn" value="{{$company->vrn}}" placeholder="Optional(VRN)" data-inputmask='"mask": "99-999999-A"' data-mask>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <button type="button" class="btn btn-warning btn-sm px-4 radius-30" onclick="showHideForm('hide')">{{trans('navmenu.btn_cancel')}}</button>
                            </div>
                        </form>
                    </div>

                    <div class="p-4 border rounded print_invoice">
                        <div class="row g-3">
                            <div class="col-md-6 p-3 text-center" style="border: 1px solid gray; border-radius: 30px;">
                                @if(!is_null($company->logo_url))
                                <figure>
                                    <img class="invoice-logo" src="{{asset('storage/clogos/'.$company->logo_url)}}" alt="" width="150">
                                </figure>
                                @endif
                                <hr>
                                @if(!is_null($company->banner_url))
                                <h5>Banner</h5>
                                <figure>
                                    <img class="invoice-logo" src="{{asset('storage/banners/'.$company->banner_url)}}" alt="" width="150">
                                </figure>
                                @endif
                                <h5 class="mb-1">{{$company->name}}</h5>
                                <span class="color-400">{{$company->slogan}}</span>
                                @if(!is_null($company->stamp))
                                <h5>Stamp</h5>
                                <figure>
                                    <img class="invoice-logo" src="{{asset('storage/stamps/'.$company->stamp)}}" alt="" width="150">
                                </figure>
                                @endif
                                <h5>TIN : {{$company->tin}}</h5>
                                <table class="table table-responsive">
                                    <thead>
                                        <tr>
                                            <th colspan="2">Contact information</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th>Mobile</th><td>{{$company->mobile}}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th><td>{{$company->email}}</td>
                                        </tr>
                                        <tr>
                                            <th>Address</th><td>{{$company->address}}</td>
                                        </tr>
                                        <tr>
                                            <th>Postal</th><td>{{$company->postal_code}}</td>
                                        </tr>
                                        <tr>
                                            <th>City</th><td>{{$company->city}}</td>
                                        </tr>
                                        <tr>
                                            <th>Country</th><td>{{$company->country}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-1 text-uppercase">Shops, Businesses & Stores</h6>
                                <table class="items mt-1">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th style="width: 10px;">#</th>
                                            <th>{{trans('navmenu.business_name')}}</th>
                                            <th>Is A Warehouse</th>
                                            <th>{{trans('navmenu.date_registered')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($shops as $index => $shop)
                                        <tr>
                                            <td>{{ $index+1  }}</td>
                                            <td><a href="{{route('shops.show' , encrypt($shop->id))}}">{{ $shop->name }}</a></td>
                                            <td>@if($shop->is_warehouse) YES @else NO @endif </td>
                                            <td>{{ $shop->created_at}} </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection