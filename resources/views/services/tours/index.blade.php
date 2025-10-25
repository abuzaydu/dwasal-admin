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
            var expList = document.getElementById('exp-item-list');
            if (elem == 'show') {
                newform.style.display = 'block';
                newtitle.style.display = 'block';
                expList.style.display = 'none';
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

        function showHideExportList(elem) {
            var newform = document.getElementById('new-form');
            var newbtn = document.getElementById('new-btn');
            var itemlist = document.getElementById('item-list');
            var newtitle = document.getElementById('new-title');
            var listtitle = document.getElementById('list-title');
            var expList = document.getElementById('exp-item-list');
            var exportBtn = document.getElementById('export-btn');
            if (elem == 'show') {
                expList.style.display = 'block';
                exportBtn.style.display = 'none';
                newform.style.display = 'none';
                newtitle.style.display = 'none';
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
                <form class="dashform row g-1" action="{{ url('f-chako-tours') }}" method="POST">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="form-group col-sm-8">
                        <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange"><span><i class="fa fa-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                    </div>
                    <!-- /.form group -->
                </form>
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
                            <div class="d-lg-flex align-items-center mb-4 gap-3">
                                <div class="position-relative">
                                    <h6 class="mb-0 text-uppercase" id="new-title" style="display: none;">New Tour</h6>
                                    <h6 class="mb-0 text-uppercase" id="list-title">Chako Tours</h6>
                                </div>
                                <div class="ms-auto">
                                    <button type="button" id="export-btn" class="btn btn-warning btn-sm" onclick="showHideExportList('show')"><i class="bx bxs-plus-square"></i>Export List</button>
                                    <button type="button" id="new-btn" class="btn btn-primary btn-sm" onclick="showHideForm('show')"><i class="bx bxs-plus-square"></i>New Tour</button>
                                </div>
                            </div>

                            <div class="p-4 border rounded" id="new-form" style="display: none;">
                                <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('chako-tours.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="col-md-3">
                                        <label for="invoice" class="form-label">Tour Category</label>
                                        <select name="category" class="form-select form-select-sm mb-1" required>
                                            <option value="">--Select Tour Category--</option>
                                            <option>Awarenance Tours</option>
                                            <option>Paid Tours</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Tour Date</label>
                                        <div class="inner-addon left-addon"> 
                                            <i class="myaddon fa fa-calendar"></i>
                                            <input type="text" name="tour_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="validationCustom01" class="form-label">Internal Guider</label>
                                        <input type="text" class="form-control form-control-sm mb-1" id="validationCustom01" name="internal_guider" placeholder="Enter name of Staff Guider" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="validationCustom01" class="form-label">External Guider</label>
                                        <input type="text" class="form-control form-control-sm mb-1" id="validationCustom01" name="external_guider" placeholder="Enter name of External Guider" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="validationCustom02" class="form-label">No. of Visitors</label>
                                        <input type="number" step="any" class="form-control form-control-sm mb-1" id="validationCustom02" name="no_of_visitors" placeholder="Enter number of Visitors">
                                    </div>
                                    <div class="col-md-9">
                                        <label for="validationCustom03" class="form-label">Comments</label>
                                        <input type="tel" class="form-control form-control-sm mb-1" id="validationCustom03" name="comments" placeholder="Enter Visitors Comments">
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary btn-sm px-4 radius-30" type="submit" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                        <button type="button" class="btn btn-warning btn-sm px-4 radius-30" onclick="showHideForm('hide')">{{trans('navmenu.btn_cancel')}}</button>
                                    </div>
                                </form>
                            </div>

                            <div class="table-responsive" id="item-list">
                                <table id="tours" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Tour Date</th>
                                            <th>Tour Category</th>
                                            <th>Internal Guider</th>
                                            <th>External Guider</th>
                                            <th>No. of Visitors</th>
                                            <th>Comments</th>
                                            <th>{{trans('navmenu.created_at')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tours as $key => $tour)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$tour->tour_date}}</td>
                                            <td>{{$tour->category}}</td>
                                            <td>{{$tour->internal_guider}}</td>
                                            <td>{{$tour->external_guider}}</td>
                                            <td style="text-align: center;">{{$tour->no_of_visitors}}</td>
                                            <td>{{$tour->comments}}</td>
                                            <td>{{$tour->created_at}}</td>
                                            <td>
                                                <a href="{{route('chako-tours.edit', encrypt($tour->id))}}">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                                </a> | 
                                                <form method="POST" action="{{route('chako-tours.destroy' , encrypt($tour->id))}}" id="delete-form-{{$key}}" style="display: inline;"> 
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
                            <div class="table-responsive" id="exp-item-list" style="display: none;">
                                <table id="export-tours" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Tour Date</th>
                                            <th>Tour Category</th>
                                            <th>Internal Guider</th>
                                            <th>External Guider</th>
                                            <th>No. of Visitors</th>
                                            <th>Comments</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tours as $key => $tour)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$tour->tour_date}}</td>
                                            <td>{{$tour->category}}</td>
                                            <td>{{$tour->internal_guider}}</td>
                                            <td>{{$tour->external_guider}}</td>
                                            <td style="text-align: center;">{{$tour->no_of_visitors}}</td>
                                            <td>{{$tour->comments}}</td>
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
@endsection
@section('page-scripts')
     <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(function(){
            $('#tours').DataTable({
                "scrollX": true,
            });
            var date = "<?php echo $start_date.' to '.$end_date; ?>"
            var shop_name = "<?php echo $shop->name; ?>";

            var exptable = $('#export-tours').DataTable({
                "scrollX": true,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "Chako Tours_" + date,
                        title: "Chako Tours",
                        messageTop: 'DATE : ' + date
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "Chako Tours_" + date,
                        title: shop_name + "\n Chako Tours \n Date : " + date,
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                    }
                ],
            });
            exptable.buttons().container().appendTo('#export-tours_wrapper .col-md-6:eq(1)');
        })
    </script>
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