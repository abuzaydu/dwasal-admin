@extends('layouts.acc')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
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


        function showHideassetForm(elem) {
            var newform = document.getElementById('new-asset-form');
            var newbtn = document.getElementById('new-asset-btn');
            var itemlist = document.getElementById('asset-list');
            var newtitle = document.getElementById('new-asset-title');
            var listtitle = document.getElementById('asset-list-title');
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


        function showHideGradeForm(elem) {
            var newform = document.getElementById('new-grade-form');
            var newbtn = document.getElementById('new-grade-btn');
            var itemlist = document.getElementById('grade-list');
            var newtitle = document.getElementById('new-grade-title');
            var listtitle = document.getElementById('grade-list-title');
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

        function confirmDeleteasset(id){

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
                document.getElementById('delete-asset-form-'+id).submit();
                Swal.fire(
                  "{{trans('navmenu.deleted')}}",
                  "{{trans('navmenu.cancelled')}}",
                  'success'
                )
              }
            })
        }

        function confirmDeleteGrade(id){
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
                document.getElementById('delete-grade-form-'+id).submit();
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
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                <a href="{{ url('dep-methods')}}" class="btn btn-warning btn-sm">Depreciation Methods</a>
                <button type="button" id="new-asset-btn" class="btn btn-primary btn-sm" onclick="showHideassetForm('show')"><i class="bx bxs-plus-square"></i>New asset</button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded" id="new-asset-form" style="display: none;">
                        <form class="form row g-3" method="POST" action="{{route('asset-records.store')}}">
                            @csrf
                            <div class="col-md-3">
                                <label class="form-label">Asset Name <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="asset_name" required placeholder="Enter asset name" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Asset Class <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="asset_class" required placeholder="Enter Asset Class" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Description <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="description" placeholder="Enter Description" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Physical Location</label>
                                <input id="name" type="text" name="physical_location" placeholder="Enter Physical Location" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Asset Number  <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="asset_number" placeholder="Enter Asset Number" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Serial Number </label>
                                <input id="name" type="text" name="serial_no" placeholder="Enter Serial Number" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Acquisition Date <span style="color: red; font-weight: bold;">*</span></label>
                                <div class="inner-addon left-addon">
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="acquisition_date" id="in_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                </div> 
                            </div>  
                            <div class="col-md-3">
                                <label class="form-label">Acquisition Cost ({{$currency}}) <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="acquisition_cost" placeholder="Enter Acquisition Cost" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Depreciation Method <span style="color: red; font-weight: bold;">*</span></label>
                                <select name="dep_method" class="form-select form-select-sm mb-1" required>
                                    <option class="">--Select--</option>
                                    @foreach($depmethods as $m)
                                    <option value="{{$m->abbreviation}}">{{$m->dep_method}} - {{$m->abbreviation}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Useful Life <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="number" name="useful_life" placeholder="Enter Useful life" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">First Year (%) </label>
                                <input id="name" type="number" name="first_year" placeholder="Enter First Year" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Salvage Value ({{$currency}}) <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="number" name="salvage_value" placeholder="Enter Acquisition Cost" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <button type="button" class="btn btn-warning btn-sm px-4 radius-30" onclick="showHideassetForm('hide')">{{trans('navmenu.btn_cancel')}}</button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive" id="asset-list">
                        <table id="assets" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Asset Name</th>
                                    <th>Asset Class</th>
                                    <th>Description</th>
                                    <th>Physical Location</th>
                                    <th>Asset Number</th>
                                    <th>Serial No.</th>
                                    <th>Acquisition Date</th>
                                    <th>Acquisition Cost</th>
                                    <th>Depreciation Method</th>
                                    <th>Useful Life</th>
                                    <th>Salvage Value</th>
                                    <th>{{trans('navmenu.actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assets as $key => $asset)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td><a href="{{ route('asset-records.show', encrypt($asset->id)) }}">{{$asset->asset_name}}</a></td>
                                    <td style="text-align: center;">{{$asset->asset_class}}</td>
                                    <td>{{$asset->description}}</td>
                                    <td style="text-align: center;">{{$asset->physical_location}}</td>
                                    <td style="text-align: center;">{{$asset->asset_number}}</td>
                                    <td style="text-align: center;">{{$asset->serial_no}}</td>
                                    <td style="text-align: center;">{{$asset->acquisition_date}}</td>
                                    <th style="text-align: center;">{{number_format($asset->acquisition_cost)}}</th>
                                    <td style="text-align: center;">{{$asset->dep_method}}</td>
                                    <td style="text-align: center;">{{$asset->useful_life}}</td>
                                    <td style="text-align: center;">{{number_format($asset->salvage_value)}}</td>
                                    <td>
                                        <a href="{{route('asset-records.edit', encrypt($asset->id))}}">
                                            <i class="fa fa-edit" style="color: blue;"></i>
                                        </a> | 
                                        <form method="POST" action="{{route('asset-records.destroy' , encrypt($asset->id))}}" id="delete-asset-form-{{$key}}" style="display: inline;"> 
                                            @csrf
                                            @method('DELETE')
                                            <a href="javascript:;" onclick="return confirmDeleteasset({{$key}})">
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
    <!--end row-->
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    
    <script>
        $(function () {
            //Exportable table
            $('#assets').DataTable({
                'scrollX': true
            });
        });
    </script>
@endsection
<link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">

<script src="{{asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="acquisition_date"]');
            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>