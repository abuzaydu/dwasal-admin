@extends('layouts.prod')

@section('content')
      <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('/prod-home')}}"><i class="fa fa-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">

        </div>
      </div>
    <!--end breadcrumb-->

    <div class="row">
      <div class="col-md-12 mx-auto">
          <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6> 
          <hr/>
          <form class="row g-3" action="{{url('rm-uses-report')}}" method="POST">
            @csrf
            <input type="hidden" name="start_date" id="start_input" value="">
            <input type="hidden" name="end_date" id="end_input" value="">
            <!-- Date and time range -->
            <div class="col-md-4 float-end">
              <div class="input-group">
                  <button type="button" class="btn btn-white float-end" id="reportrange">
                    <span><i class="fa fa-calendar"></i></span>
                    <i class="fa fa-caret-down"></i>
                  </button>
                </div>
            </div>
            <!-- /.form group -->

            <div class="form-group col-md-5 float-end">
              <select name="rm_id" class="form-select form-select-sm select2">
                @if(!is_null($raw_material))
                <option value="{{$raw_material->id}}">{{$raw_material->name}}</option>
                @else
                <option value="">{{trans('navmenu.select_rm')}}</option>
                @endif
                @foreach($raw_materials as $raw)
                <option value="{{$raw->id}}">{{$raw->name}}</option>
                @endforeach
              </select>
            </div>
          </form>

          <!-- title row -->
          <div class="row">
            <div class="col-md-12">
              <div class="card radius-6">
                <div class="card-body">
                    <div id="printThis">
                      <div class="row">
                        <div style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                          @if(!is_null($shop->logo_location))
                          <figure>
                              <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                          </figure>
                          @endif
                          <h6>{{$shop->name}}</h6>
                          <h5 class="title">{{trans('navmenu.rm_uses_report')}}</h5>
                          <p> @if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</p>          
                        </div>
                      </div>
                      <!-- Table row -->
                      <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="stocktaking" class="table table-striped display nowrap" style="width: 100%;">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                                  <th>{{trans('navmenu.name')}}</th>
                                  <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                  <th style="text-align: center;">{{trans('navmenu.total_cost')}}</th>
                                  <th style="text-align: center;">{{trans('navmenu.batch_no')}}</th>    
                                </tr>
                                </thead>
                                <tbody>
                                  @foreach($rm_uses as $index => $rm_use)
                                  <tr>
                                    <td>{{$index+1}}</td>
                                    <td style="text-align: center;">{{$rm_use->date}}</td>
                                    <td>{{$rm_use->name}}</td>
                                    <td style="text-align: center;">
                                     {{$rm_use->quantity}}
                                    </td>
                                    <td style="text-align: center;">{{number_format($rm_use->total_cost)}}</td>
                                    <td style="text-align: center;">{{$rm_use->prod_batch}}</td>
                                  </tr>
                                  @endforeach
                                </tbody>
                                <tfoot>
                                  <th></th>
                                  <th></th>
                                  <th></th>
                                  <th></th>
                                  <th style="text-align: center;">{{number_format($total)}}</th>
                                  <th></th>
                                </tfoot>
                            </table>
                          </div>
                        </div>
                        <!-- /.col -->
                      </div>
                      <!-- /.row -->
                      <div class="row" style="border-top: 2px solid #82B1FF;">
                        <div class="col-xs-6">
                          <span>{{Session::get('shop_name')}} reports</span>
                        </div>
                        <div class="col-xs-6">
                          <span class="float-end">Generated on: {{$reporttime}}</span>
                        </div>
                      </div>
                      
                    </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div>
              <!-- /.nav-tabs-custom -->
            </div>
            <!-- col -->
          </div>
        </div>
      </div>
      <!-- row -->
  </section>
@endsection

<script language="javascript" type="text/javascript">
        function printDiv(divID) {
            //Get the HTML of div
            var divElements = document.getElementById(divID).innerHTML;
            //Get the HTML of whole page
            var oldPage = document.body.innerHTML;

            //Reset the page's HTML with div's HTML only
            document.body.innerHTML = 
              "<html><head><title></title></head><body>" + 
              divElements + "</body>";

            //Print Page
            window.print();

            //Restore orignal HTML
            document.body.innerHTML = oldPage;

          
        }
</script>

<link rel="stylesheet" href="css/DatePickerX.css">

<script src="js/DatePickerX.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="start_date"]'),
                $max = document.querySelector('[name="end_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                minDate    : new Date("<?php echo date('Y-m-d', strtotime($shop->created_at)); ?>"),
                format     : 'yyyy-mm-dd',
                maxDate    : $max
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : $min,
                maxDate    : new Date()
            });

        });
    </script>