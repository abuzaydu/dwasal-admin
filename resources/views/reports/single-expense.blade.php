@extends('layouts.gen')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('reports') }}">Reports </a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right pt-3">
               <form class="dashform form-horizontal" action="{{url('single-expense-report/'.$type)}}" method="POST">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="col-md-12 float-end">
                        <div class="input-group">
                            <button type="button" class="btn btn-white float-end" id="reportrange">
                                <span><i class="fa fa-calendar"></i></span>
                                <i class="fa fa-caret-down"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.form group -->
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
            	<div class="card-body">
	                <div class="col-xs-12 table-responsive">
		                <table id="sexpenses" class="table table-striped display nowrap" style="width: 100%;">
		                	<thead>
	                          	<tr>
	                            	<th>#</th>
	                            	<th>{{trans('navmenu.date')}}</th>
	                            	<th>{{trans('navmenu.amount')}}</th>
	                            	<th>{{trans('navmenu.description')}}</th>
	                          	</tr>
	                        </thead>
	                        <tbody>
	                          	@foreach($texpenses as $index => $expense)
	                          	<tr>
	                           		<td>{{$index+1}}</td>
	                           		<td>{{$expense->date}}</td>
	                           		<td>{{number_format($expense->amount)}}</td>
	                           		<td>{{$expense->description}}</td>
	                          	</tr>
	                          	@endforeach
	                        </tbody>
	                        <tfoot>
	                          	<tr>
	                           		<th></th>
	                           		<th>{{trans('navmenu.total')}}</th>
	                           		<th>{{number_format($total)}}</th>
	                           		<th></th>
	                          	</tr>
	                        </tfoot>
	                    </table>
	                </div>
            	</div>
            	<!-- /.tab-content -->
          	</div>
          	<!-- /.nav-tabs-custom -->
        </div>
        <!-- col -->
    </div>
    <!-- row -->
@endsection
@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(function(){
            var d = new Date();
            const months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
            var day = d.getDate();
            var month = d.getMonth();
            var year = d.getFullYear();
            var date = day + " " + months[month] + " " + year;
            var duration = "<?php echo $duration; ?>";
            var shop_name = "<?php echo $shop->name; ?>";

        });
    </script>
@endsection