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


        function showHidedepreciationForm(elem) {
            var newform = document.getElementById('new-depreciation-form');
            var newbtn = document.getElementById('new-depreciation-btn');
            var itemlist = document.getElementById('depreciation-list');
            var newtitle = document.getElementById('new-depreciation-title');
            var listtitle = document.getElementById('depreciation-list-title');
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

        function confirmDeletedepreciation(id){

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
                document.getElementById('delete-depreciation-form-'+id).submit();
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
                <a href="{{ url('asset-records')}}" class="btn btn-warning btn-sm">Asset Records</a>
                <button type="button" id="new-depreciation-btn" class="btn btn-primary btn-sm" onclick="showHidedepreciationForm('show')"><i class="bx bxs-plus-square"></i>New depreciation</button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded" id="new-depreciation-form" style="display: none;">
                        <form class="form row g-3" method="POST" action="{{route('depreciations.store')}}">
                            @csrf
                            <div class="col-md-4">
                                <label class="form-label">Asset Name<span style="color: red; font-weight: bold;">*</span></label>
                                <select name="asset_record_id" class="form-select form-select-sm mb-1" required>
                                    <option value="">--Select--</option>
                                    @foreach($assets as $asset)
                                    <option value="{{$asset->id}}">{{$asset->asset_number}} - {{$asset->asset_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Year <span style="color: red; font-weight: bold;">*</span></label>
                                <select name="year" class="form-select form-select-sm ">
                                    <option value="">--Select--</option>
                                    @foreach($years as $y)
                                    <option>{{$y}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <button type="button" class="btn btn-warning btn-sm px-4 radius-30" onclick="showHideDevceForm('hide')">{{trans('navmenu.btn_cancel')}}</button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive" id="depreciation-list">
                        <table id="depreciations" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Asset Number</th>
                                    <th>Asset Name</th>
                                    <th>Year</th>
                                    <!-- <th>Acquisition Cost</th> -->
                                    <th>Value Begin of Year ({{$currency}}) </th>
                                    <th>Depreciation ({{$currency}})</th>
                                    <th>Value End of Year ({{$currency}})</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($depreciations as $key => $depreciation)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$depreciation->asset_number}}</a></td>
                                    <td>{{$depreciation->asset_name}}</td>
                                    <td>{{$depreciation->year}}</td>
                                    <!-- <th style="text-align: center;">{{number_format($depreciation->acquisition_cost, 2, '.', ',')}}</th> -->
                                    <td style="text-align: center;">{{number_format($depreciation->value_begin_yr, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($depreciation->dep_amount, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($depreciation->value_end_yr, 2, '.', ',')}}</td>
                                    <td>
                                        <a href="{{route('depreciations.edit', encrypt($depreciation->id))}}">
                                            <i class="fa fa-edit" style="color: blue;"></i>
                                        </a> | 
                                        <form method="POST" action="{{route('depreciations.destroy' , encrypt($depreciation->id))}}" id="delete-depreciation-form-{{$key}}" style="display: inline;"> 
                                            @csrf
                                            @method('DELETE')
                                            <a href="javascript:;" onclick="return confirmDeletedepreciation({{$key}})">
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
            $('#depreciations').DataTable({
                'scrollX': true
            });
        });
    </script>
@endsection