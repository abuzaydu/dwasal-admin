@extends('layouts.sand')
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
    <div class="block-header">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-dash') }}"><i class="icon-home"></i></a></li>   
                    <li class="breadcrumb-item">Washed Sand Productions</li>                         
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#qtestModal"><i class="fa fa-check-circle"></i> New Quality Test</button>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#endProdModal"><i class="fa fa-plus"></i> New End Product</button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" id="prodrun-list">
                        <table id="prodruns" class="table table-bordered display nowrap" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <th>Source Raw Material Storage</th>
                                    <td>{{$prodrun->location_name}}</td>
                                </tr>
                                <tr>
                                    <th>PR Batch No.</th>
                                    <td>{{$prodrun->pr_no}}</td>
                                </tr>
                                <tr>
                                    <th>Start Time</th>
                                    <td>{{$prodrun->start_time}}</td>
                                </tr>
                                <tr>
                                    <th>End Time</th>
                                    <td>{{$prodrun->end_time}}</td>
                                </tr>
                                <tr>
                                    <th>Input Quntity</th>
                                    <td>{{$prodrun->input_quantity+0}}</td>
                                </tr>
                                <tr>
                                    <th>Output Quantity</th>
                                    <td>{{$prodrun->output_quantity+0}}</td>
                                </tr>
                                <tr>
                                    <th>Waste Water Quantity</th>
                                    <td>{{$prodrun->waste_water_quantity+0}}</td>
                                </tr>
                                <tr>
                                    <th>Created By</th>
                                    <td>{{$prodrun->name}}</td>
                                </tr>
                                <tr>
                                    <th colspan="2" style="border-top: 2px solid gray; border-bottom: 2px solid gray;">Stock Movement Summary</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="card mt-0">
                <div class="card-body">
                    <ul class="nav nav-tabs-new2">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab_0-0"><i class='fa fa-list'></i> Quality Tests </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab_1-1"><i class='fa fa-list-alt'></i> End Product</a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="tab_0-0" role="tabpanel">
                            <div class="table-responsive">
                                <table id="rm-sourcings" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Test Date</th>
                                            <th>Test Type</th>
                                            <th>Result</th>
                                            <th>Passed?</th>
                                            <th>Test Record By</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($qualitytests as $key => $qtest)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$qtest->test_date}}</td>
                                            <td>{{$qtest->test_type}}</td>
                                            <td>{{$qtest->result}}</td>
                                            <td style="text-align: center;">
                                                @if($qtest->passed)
                                                Yes
                                                @else
                                                No
                                                @endif
                                            </td>
                                            <td>{{$qtest->first_name}} {{$qtest->last_name}}</td>
                                            <td style="text-align: center;">
                                                <a href="{{route('quality-tests.edit', encrypt($qtest->id))}}">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                                </a> | 
                                                <form method="POST" action="{{route('quality-tests.destroy' , encrypt($qtest->id))}}" id="delete-form-{{$key}}" style="display: inline;"> 
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
                        <div class="tab-pane fade" id="tab_1-1" role="tabpanel">
                            <div class="table-responsive">
                                <table id="example2" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                                        <th>{{trans('navmenu.name')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.source')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.actions')}}</th>
                                    </thead>
                                    <tbody>
                                        @foreach($stocks as $index => $stock)
                                        <tr>
                                            <td style="text-align: center;">{{$index+1}}</td>
                                            <td>{{date('d-m-Y', strtotime($stock->stock_date))}}</td>
                                            <td>{{ $stock->slug}}</td>
                                            <td style="text-align: center;">
                                                {{$stock->quantity_in+0}}
                                            </td>
                                            <td>{{$stock->source}}</td>
                                            <td>
                                                @if(is_null($stock->purchase_id))
                                                @if(Auth::user()->can('edit-stock'))
                                                <a href="{{route('stocks.edit' , encrypt($stock->id))}}"><i class="fa fa-edit" style="color: blue;"></i></a>
                                                @endif
                                                @if(Auth::user()->can('delete-stock'))
                                                <form id="delete-form-{{$index}}" method="POST" action="{{route('stocks.destroy' , encrypt($stock->id))}}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" class="text-danger" onclick=" return confirmDelete({{$index}})"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                                </form>    
                                                @endif
                                                @endif
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
    <div class="modal animated zoomIn" id="qtestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quality Test</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form" method="POST" action="{{ route('quality-tests.store') }}">
                        @csrf
                        <input type="hidden" name="production_run_id" value="{{$prodrun->id}}">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label">Test Date</label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="test_date" id="test_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                  <label class="form-label">Test Type <span style="color: red; font: bold;">*</span></label>
                                  <select name="test_type" class="form-select form-select-sm mb-1" required>
                                      <option value="">--Select--</option>
                                      <option>Visual Inspection</option>
                                      <option>Sieve Analysis</option>
                                      <option>Moisture Content</option>
                                      <option>Clay and Silt Content</option>
                                      <option>Organic Matter</option>
                                      <option>Permeability Test</option>
                                      <option>Specific Gravity</option>
                                      <option>Chemical Analysis</option>
                                      <option>Testing Frequency</option>
                                  </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Result</label>
                                <input type="text" name="result" placeholder="Please enter Test Results" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-6">
                                  <label class="form-label">Passed</label>
                                  <select name="passed" class="form-select form-select-sm mb-1" required>
                                      <option value="1">Yes</option>
                                      <option value="0">No</option>
                                  </select>
                            </div>
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                            </div>         
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal animated zoomIn" id="endProdModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">End Product</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form" method="POST" action="{{ route('stocks.store') }}">
                        @csrf
                        <input type="hidden" name="production_run_id" value="{{$prodrun->id}}">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label">Stock Date</label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="stock_date" id="stock_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                  <label class="form-label">Product <span style="color: red; font: bold;">*</span></label>
                                  <select name="product_id" class="form-select form-select-sm mb-1" required>
                                      <option value="">--Select--</option>
                                      @foreach($sandproducts as $key => $product)
                                      <option value="{{$product->id}}">{{ $product->slug}} {{$product->basic_uom}}</option>
                                      @endforeach
                                  </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Quantity</label>
                                <input type="text" name="quantity_in" placeholder="Please enter End Product Quantity" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Storage Location<span style="color: red; font-weight: bold;">*</span></label>
                                <select name="storage_location_id" class="form-select form-select-sm mb-1" required>
                                    <option value="">--Select--</option>
                                    @foreach($slocations as $key => $location)
                                    @if($slocations->count() == 1)
                                    <option value="{{$location->id}}" selected>{{$location->location_name}}</option>
                                    @else
                                    <option value="{{$location->id}}">{{$location->location_name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                            </div>         
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="test_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            var $sdate = document.querySelector('[name="stock_date"]');

            $sdate.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>