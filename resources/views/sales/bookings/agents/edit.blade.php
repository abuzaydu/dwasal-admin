@extends('layouts.app')
    <script>
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

        function confirmDelete(id){
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
    </script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('booking-agents') }}">Booking Agents</a></li>                         
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content py-1">
                        <div class="tab-pane fade show active" id="tab_0" role="tabpanel">
                            <div class="p-4 border rounded">
                                <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('booking-agents.update', encrypt($bagent->id)) }}" enctype="multipart/form-data">
                                    @csrf
                                    {{ method_field('PATCH') }}
                                    <div class="col-md-3">
                                        <label class="form-label">Name <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control form-control-sm mb-1" id="validationCustom01" name="name" value="{{$bagent->name}}" placeholder="Enter agent Name" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Mobile </label>
                                        <input type="text" class="form-control form-control-sm mb-1" id="validationCustom01" name="mobile" value="{{$bagent->mobile}}" placeholder="Enter Agent Mobile ">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Email </label>
                                        <input type="text" class="form-control form-control-sm mb-1" id="validationCustom01" name="email" value="{{$bagent->email}}" placeholder="Enter Agent Email address">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Address </label>
                                        <input type="text" class="form-control form-control-sm mb-1" id="validationCustom01" name="address" value="{{$bagent->address}}" placeholder="Enter Agent Addres">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">{{trans('navmenu.tin')}}</label>
                                        <input id="tin" type="text" name="tin" placeholder="{{trans('navmenu.hnt_customer_tin')}}" value="{{$bagent->tin}}" class="form-control form-control-sm mb-1" data-inputmask='"mask": "999-999-999"' data-mask>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">{{trans('navmenu.vrn')}}</label>
                                        <input id="vrn" type="text" name="vrn" placeholder="{{trans('navmenu.hnt_customer_vrn')}}" value="{{$bagent->vrn}}" class="form-control form-control-sm mb-1" data-inputmask='"mask": "99-999999-A"' data-mask>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label">{{trans('navmenu.currency')}}</label>
                                        <select name="currency" id="currency" class="form-select form-select-sm mb-1" required>
                                            @foreach($currencies as $curr)
                                            @if($curr->code == $bagent->currency)
                                            <option selected>{{$curr->code}}</option>
                                            @else
                                            <option>{{$curr->code}}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary btn-sm px-4 radius-30" type="submit" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                        <a href="{{ url('booking-agents')}}" class="btn btn-warning btn-sm px-4 radius-30">{{trans('navmenu.btn_cancel')}}</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection