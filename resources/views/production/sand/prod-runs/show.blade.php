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
                                            <th>Created By</th>
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
                                            <td>{{$qtest->passed}}</td>
                                            <td>{{$qtest->name}}</td>
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

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection