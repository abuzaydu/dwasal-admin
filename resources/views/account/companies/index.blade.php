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
    <div class="block-header pt-1">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>               
                    <li class="breadcrumb-item">Account & Settings</li>             
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                <button type="button" id="new-btn" class="btn btn-primary btn-sm" onclick="showHideForm('show')"><i class="fa fa-plus"></i> New Company</button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="row g-3 clearfix" id="new-form" style="display: none;">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="p-4 border rounded">
                                <form class="form row g-3" method="POST" action="{{route('user-companies.store')}}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="col-md-3">
                                        <label class="form-label">Company Name<span style="color: red; font-weight: bold;">*</span></label>
                                        <input id="name" type="text" name="name" required placeholder="Enter Your Company Name" class="form-control form-control-sm mb-1">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Company Slogan</label>
                                        <input id="name" type="text" name="slogan" placeholder="Enter Your Company Slogan" class="form-control form-control-sm mb-1">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Company Logo</label>
                                        <input type="file" name="logo" class="form-control form-control-sm mb-1">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Show Name on ID Card<span style="color: red; font-weight: bold;">*</span></label>
                                        <select name="show_name_on_id_card" class="form-select form-select-sm mb-1" required>
                                            <option value="1" selected>YES</option>
                                            <option value="0">NO</option>
                                        </select>
                                        <small class="text-muted" style="font-size: 0.7rem;">Select 'NO' if the logo has text.</small>
                                    </div>
                                    
                                    <div class="col-md-3">
                                    <label class="form-label">Brand Color<span style="color: red; font-weight: bold;">*</span></label>
                                    <input  value="#FFA733" type="color" name="brand_color" required placeholder="Choose Your Company Brand Color" class="form-control form-control-sm mb-1">
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                        <button type="button" class="btn btn-warning btn-sm px-4 radius-30" onclick="showHideForm('hide')">{{trans('navmenu.btn_cancel')}}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row row-cols-xxl-4 row-cols-xl-3 row-cols-lg-2 row-cols-md-2 row-cols-sm-2 row-cols-1 g-2 mb-4 row-deck">
                @foreach($companies as $key => $company)
                <div class="col"> 
                    <div class="card text-center">
                        <div class="card-body py-4">
                            @if(!is_null($company->logo_url))
                            <figure>
                            @if($company->logo_url)
                                <img class="invoice-logo" src="{{ asset('storage/clogos/' . $company->logo_url) }}" alt="Company Logo" width="150">
                            @endif                            </figure>
                            @endif
                        </div>
                        <div class="card-footer border-bottom border-top py-3">
                            <h5 class="mb-1"><a href="{{ route('user-companies.show', encrypt($company->id)) }}">{{$company->name}}</a></h5>
                            <span class="color-400">{{$company->slogan}}</span>
                        </div>
                        <div class="card-body d-flex justify-content-between text-center">
                            <div class="flex-fill">
                                <i class="fa fa-briefcase fa-lg"></i>
                                <h6 class="mb-0 mt-2">{{$company->shops()->count()}} Shops</h6>
                            </div>
                            <div class="flex-fill">
                                <i class="fa fa-users fa-lg"></i>
                                <h6 class="mb-0 mt-2">{{$company->users()->count()}} Users</h6>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div> <!-- .row end -->
        </div>
    </div>
@endsection