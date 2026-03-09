@extends('layouts.vml')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
    <script>
        function showHideForm(elem) {
            var newform = document.getElementById('new-form');
            var newbtn = document.getElementById('new-btn');

            if (elem == 'show') {
                newform.style.display = 'block';
                newbtn.style.display = 'none';
            }else{
                newform.style.display = 'none';
                newbtn.style.display = 'block';
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
                    <li class="breadcrumb-item"><a href="{{ url('prod-dash') }}"><i class="fa fa-home"></i></a></li>   
                    <li class="breadcrumb-item">Visitors Managment</li>                         
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12">

            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form class="form row g-3" method="POST" action="{{ route('visitors.update', encrypt($visitor->id)) }}" enctype="multipart/form-data">
                        @csrf
                        {{ method_field('PATCH') }}
                        <div class="col-sm-3">
                            <label class="form-label">Badge Number<span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="text" name="badge_no" value="{{$visitor->badge_no}}" placeholder="Enter visitor Plate Number" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Name <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="location" type="text" name="name" value="{{$visitor->name}}" placeholder="Enter visitor Name" class="form-control form-control-sm mb-1" required>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Mobile Number  <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="location" type="text" name="mobile" value="{{$visitor->mobile}}" placeholder="Enter Mobile Number" class="form-control form-control-sm mb-1" required>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Email Address </label>
                            <input id="location" type="email" name="email" value="{{$visitor->email}}" placeholder="Enter Email Address" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Address <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="location" type="text" name="address" value="{{$visitor->address}}" placeholder="Enter Visitor Address" class="form-control form-control-sm mb-1" required>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Purpose <span style="color: red; font-weight: bold;">*</span> </label>
                            <input id="capacity" type="text" name="purpose" value="{{$visitor->purpose}}" placeholder="Enter Visitor Purpose" class="form-control form-control-sm mb-1" required>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">{{trans('navmenu.cust_id_type')}}</label>
                            <select class="form-select form-select-sm mb-1" name="id_type">
                                @foreach($visitorids as $cid)
                                @if($cid['name'] == $visitor->id_type)
                                <option selected>{{$cid['name']}}</option>
                                @else
                                <option>{{$cid['name']}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">{{trans('navmenu.id_number')}}</label>
                            <input type="text" name="id_number" value="{{$visitor->id_number}}" placeholder="{{trans('navmenu.hnt_id_number')}}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Host <span style="color: red; font-weight: bold;">*</span></label>
                            <select id="unit" name="host_id" class="form-select form-select-sm mb-1" required>
                                <option value="">-- Select Host --</option>
                                @foreach($employees as $emp)
                                @if($emp->id == $visitor->host_id)
                                <option value="{{ $emp->id }}" selected>{{ $emp->fname }} {{ $emp->lname }}</option>
                                @else
                                <option value="{{ $emp->id }}">{{ $emp->fname }} {{ $emp->lname }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Department </label>
                            <select id="unit" name="ownership_id" class="form-select form-select-sm mb-1"d>
                                <option value="">--Select--</option>
                                @foreach($departments as $key => $dept)
                                @if($dept->id == $visitor->department_id)
                                <option value="{{ $dept->id }}" selected>{{$dept->name}}</option>
                                @else
                                <option value="{{ $dept->id }}">{{$dept->name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">The items he/she came in with</label>
                            <input type="text" name="came_in_with" value="{{$visitor->came_in_with}}" class="form-control form-control-sm mb-1" placeholder="Enter Visitor Items Eg. Laptops, Phone, ...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">The items he/she came out with</label>
                            <input type="text" name="came_out_with" value="{{$visitor->came_out_with}}" class="form-control form-control-sm mb-1" placeholder="Enter Visitor Items Eg. Laptops, Phone, ...">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">Save Changes</button>
                            <a href="{{ url()->previous() }}" class="btn btn-warning btn-sm">
                                {{ trans('navmenu.btn_cancel') }}
                            </a>                       ,
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection

<link rel="stylesheet" href="{{ asset('css/DatePickerX.css')}}">
<script src="{{ asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="reg_date"]');
            $min.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>
