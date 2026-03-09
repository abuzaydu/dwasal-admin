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
    <div class="block-header pt-4 py-lg-4 py-3">
        <div class="row g-3 align-items-center">

            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item">
                        <a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{ url('prod-dash') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Visitors Management</li>
                    <li class="breadcrumb-item active">{{ $page }}</li>
                </ul>
            </div>

            <div class="col-md-6 col-sm-12 text-md-end d-flex align-items-center justify-content-md-end gap-2 flex-wrap">

                <form class="dashform report-form d-inline"
                    action="{{ route('visitors.filter') }}"
                    method="POST">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="{{ $start_date }}">
                    <input type="hidden" name="end_date"   id="end_input"   value="{{ $end_date }}">
                    <button type="button" class="btn btn-white btn-sm" id="reportrange">
                        <span><i class="fa fa-calendar"></i></span>
                        <i class="fa fa-caret-down"></i>
                    </button>
                </form>

                <button type="button" class="btn btn-primary btn-sm"
                        data-bs-toggle="modal" data-bs-target="#visitorModal">
                    <i class="fa fa-plus-square"></i> New Visitor
                </button>

            </div>

        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2">
                        <li class="nav-item"><a class="nav-link active show" data-bs-toggle="tab" href="#tab_0">Visitors List</a></li>
                    </ul>
                    <div class="tab-content pt-2">
                        <div class="tab-pane fade show active" id="tab_0" role="tabpanel">
                            <div class="table-responsive" id="visitor-list">
                                <table id="visitors" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>visitor Name</th>
                                            <th>Mobile</th>
                                            <th>Address</th>
                                            <th>ID Type</th>
                                            <th>ID Number</th>
                                            <th>Purpose</th>
                                            <th>Host</th>
                                            <th>Status</th>
                                            <th>Time IN</th>
                                            <th>Time Out</th>
                                            <th>Created At</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($visitors as $key => $visitor)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td><a href="{{ route('visitors.show', encrypt($visitor->id))}}">{{$visitor->name}}</a></td>
                                            <td>{{$visitor->mobile}}</td>
                                            <td>{{$visitor->address}}</td>
                                            <td>{{$visitor->id_type}}</td>
                                            <td>{{$visitor->id_number}}</td>
                                            <td>{{$visitor->purpose}}</td>
                                            <td>{{$visitor->fname}} {{$visitor->lname}}</td>
                                            <td>{{$visitor->status}}</td>
                                            <td>@if(!empty($visitor->time_in)) {{date('d/m/Y', strtotime($visitor->time_in)) }}@endif</td>
                                            <td>@if(!empty($visitor->time_out)) {{date('d/m/Y', strtotime($visitor->time_out)) }}@endif</td>
                                            <td>{{$visitor->created_at}}</td>
                                            <td style="text-align: center;">
                                                <a href="{{route('visitors.edit', encrypt($visitor->id))}}">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                                </a> | 
                                                <form method="POST" action="{{route('visitors.destroy' , encrypt($visitor->id))}}" id="delete-form-{{$key}}" style="display: inline;"> 
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDelete({{$key}})">
                                                        <i class="fa fa-trash" style="color: red;"></i>
                                                    </a>                        
                                                </form>    
                                            </td>
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
    <!--end row-->


    <!-- Modal -->
    <div class="modal animated zoomIn" id="visitorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-m">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New visitor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form row g-3" method="POST" action="{{route('visitors.store')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">Badge Number<span style="color: red; font-weight: bold;">*</span></label>
                            <select name="badge_no" class="form-select form-select-sm mb-1" required>
                                <option value="">-- Select Badge --</option>
                                @foreach($badges as $badge)
                               
                                <option value="{{ $badge->badge_number }}">{{ $badge->badge_number }}</option>
                                @endforeach
                                
                            </select>
                          
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Name <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="location" type="text" name="name" placeholder="Enter visitor Name" class="form-control form-control-sm mb-1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mobile Number  <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="location" type="text" name="mobile" placeholder="Enter Mobile Number" class="form-control form-control-sm mb-1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address </label>
                            <input id="location" type="email" name="email" placeholder="Enter Email Address" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="location" type="text" name="address" placeholder="Enter Visitor Address" class="form-control form-control-sm mb-1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Purpose <span style="color: red; font-weight: bold;">*</span> </label>
                            <input id="capacity" type="text" name="purpose" placeholder="Enter Visitor Purpose" class="form-control form-control-sm mb-1" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.cust_id_type')}}</label>
                            <select class="form-select form-select-sm mb-1" name="id_type">
                                @foreach($visitorids as $cid)
                                <option>{{$cid['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.id_number')}}</label>
                            <input type="text" name="id_number" placeholder="{{trans('navmenu.hnt_id_number')}}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Host <span style="color: red; font-weight: bold;">*</span></label>
                            <select id="unit" name="host_id" class="form-select form-select-sm mb-1" required>
                                <option value="">-- Select Host --</option>
                                @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->fname }} {{ $emp->lname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department </label>
                            <select id="unit" name="ownership_id" class="form-select form-select-sm mb-1">
                                <option value="">--Select--</option>
                                @foreach($departments as $key => $dept)
                                <option value="{{ $dept->id }}">{{$dept->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">The items he/she came in with</label>
                            <input type="text" name="came_in_with" class="form-control form-control-sm mb-1" placeholder="Enter Visitor Items Eg. Laptops, Phone, ...">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">Add</button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{ trans('navmenu.btn_cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
     <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>

    <script>
        $(function(){
            $('#visitors').DataTable();
        })
    </script>
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
