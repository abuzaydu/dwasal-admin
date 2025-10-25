@extends('layouts.inv')

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
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-3">

            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-11 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content py-1">
                        <div class="tab-pane fade show active" id="services" role="tabpanel">
                            <div class="p-4 border rounded">
                                <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('chako-tours.update', encrypt($tour->id)) }}" enctype="multipart/form-data">
                                    @csrf
                                    {{ method_field('PATCH') }}
                                    <div class="col-md-3">
                                        <label for="invoice" class="form-label">Tour Category</label>
                                        <select name="category" class="form-select form-select-sm mb-1" required>
                                            @if($tour->category == 'Awarenance Tours')
                                            <option>Awarenance Tours</option>
                                            <option>Paid Tours</option>
                                            @else
                                            <option>Paid Tours</option>
                                            <option>Awarenance Tours</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Tour Date</label>
                                        <div class="inner-addon left-addon"> 
                                            <i class="myaddon fa fa-calendar"></i>
                                            <input type="text" name="tour_date" value="{{$tour->tour_date}}" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="validationCustom01" class="form-label">Internal Guider</label>
                                        <input type="text" class="form-control form-control-sm mb-1" id="validationCustom01" name="internal_guider" value="{{$tour->internal_guider}}" placeholder="Enter name of Staff Guider" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="validationCustom01" class="form-label">External Guider</label>
                                        <input type="text" class="form-control form-control-sm mb-1" id="validationCustom01" name="external_guider" value="{{$tour->external_guider}}" placeholder="Enter name of External Guider" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="validationCustom02" class="form-label">No. of Visitors</label>
                                        <input type="number" step="any" class="form-control form-control-sm mb-1" id="validationCustom02" name="no_of_visitors" value="{{$tour->no_of_visitors}}" placeholder="Enter number of Visitors">
                                    </div>
                                    <div class="col-md-9">
                                        <label for="validationCustom03" class="form-label">Comments</label>
                                        <input type="tel" class="form-control form-control-sm mb-1" id="validationCustom03" name="comments" placeholder="Enter Visitors Comments" value="{{$tour->comments}}">
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary btn-sm px-4 radius-30" type="submit" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                        <button type="button" class="btn btn-warning btn-sm px-4 radius-30" onclick="showHideForm('hide')">{{trans('navmenu.btn_cancel')}}</button>
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
<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="tour_date"]');
            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>