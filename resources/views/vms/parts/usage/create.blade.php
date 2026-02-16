@extends('layouts.vms')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/parts.js')}}"></script>
    <script>
        
        function confirmCancel(id) {
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
                window.location.href="{{url('cancel-part-usage')}}/"+id;
                Swal.fire(
                  "{{trans('navmenu.deleted')}}",
                  "{{trans('navmenu.cancelled')}}",
                  'success'
                )
              }
            })
        }

        function submitTemp(index) {
            document.getElementById('ptemp-form-'+index).submit();
        }
    </script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Vehicle Management</li>
                    <li class="breadcrumb-item"><a  href="{{ url('parts-usage') }}">Part Usage</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-12 col-md-12 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-xl-12 mx-auto">
            <div class=" card radius-6">
                <!-- /.box-header -->
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="needs-validation row mb-1" id="stockform" method="POST" action="{{route('parts-usage.store')}}">
                            @csrf
                            <input type="hidden" name="part_usage_id" placeholder="" value="{{$pusage->id}}" class="form-control form-control-sm mb-1">
                            <div class="col-sm-12" id="ermsg"></div>
                            <div class="col-sm-3">
                                <label for="vehicle_id" class="form-label">Vehicle <span style="color: red;">*</span></label>
                                <select name="vehicle_id" id="vehicle_id" required class="form-select form-select-sm mb-1">
                                    <option value="">Select Vehicle</option>
                                    @foreach($vehicles as $vehicle)
                                    <option value="{{$vehicle->id}}">{{$vehicle->plate_no}} {{$vehicle->vehicle_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">Date <span style="color: red;">*</span></label>
                                <div class="inner-addon left-addon">
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="pu_date" id="pu_date" placeholder="{{trans('navmenu.pick_date')}}" value="{{$pusage->pu_date}}" class="form-control form-control-sm mb-1" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label for="comments" class="form-label">Remarks</label>
                                <input class="form-control form-control-sm mb-1" name="remarks" id="remarks">
                            </div>
                        </form>
                        <div class="row mb-1">
                            <form id="part-form" action="{{ route('parts-usage-items.store')}}" method="POST">
                                @csrf
                                <input type="hidden" name="part_usage_id" placeholder="" value="{{$pusage->id}}" class="form-control form-control-sm mb-1">
                                <input type="hidden" name="pu_date" value="{{$pusage->pu_date}}">
                                <div class="col-sm-8">
                                    <label class="form-label">{{trans('navmenu.search_tap')}}</label>
                                    <div class="input-group mb-0">
                                        <input type="text" class="form-control form-control-sm mb-1" id="search_key" placeholder="Search Part" autocomplete="off" aria-label="Recipient's username" aria-describedby="button-addon2">
                                        <a class="btn btn-danger btn-sm empty-search mb-1" id="button-addon2"><i class='fa fa-close'></i></a>
                                    </div>
                                    <ul id="searchResult3" class="list-group"></ul>
                                </div>
                                <input type="hidden" name="part_id" id="part-id">
                            </form>
                        </div>
                        <div class="row mb-1">
                            <div id="msg">
                                
                            </div>
                            <div class="col-md-12">
                                <table class="table table-striped" style="width: 100%;">
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th>Category</th>
                                        <th style="text-align: left;">Item</th>
                                        <th style="text-align: center;">UOM</th>
                                        <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                        <th style="text-align: center;">&nbsp;</th>
                                    </tr>
                                    @foreach($puitems as $key => $item)
                                    <tr>
                                        <td>{{$key + 1}}</td>
                                        <td>{{$item->category}}</td>
                                        <td>{{$item->part_no}} {{$item->part_name}}</td>
                                        <td style="text-align: center;">{{$item->uom}}</td>
                                        <td style="text-align: center;"><input class="edit" id="qty_{{$item->id}}" type="number" name="pu_qty"  min="0" value="{{$item->pu_qty+0}}" step="any" style="text-align:center; height: 20px; width: 160px; border: 1px solid #e0e0e0;" autocomplete="off"></td>
                                        <td><a href="#" ><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-sm-6"></div>
                            <div class="col-sm-6 pt-4">
                                <button onclick="confirmCancel('<?php echo encrypt($pusage->id); ?>')" type="button" class="btn btn-warning btn-sm float-end" style="margin-left: 5px;">{{trans('navmenu.btn_cancel')}}</button>
                                <a id="btn-submit" class="btn btn-success btn-sm float-end">{{trans('navmenu.btn_submit')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </div>      
    </div>
@endsection
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            // $('#search-form').on('submit', function(e){
                // e.preventDefault();
            $('#search_key').on('keyup',function () {
                var query = $('#search_key').val();
                $.ajax({
                    url:"{{ url('search-part') }}",
                    type:'GET',
                    data:{'search_key':query},
                    success:function (response) {
                        // $('#part_list').html(data);
                        var len = response.length;
                        $("#searchResult3").empty();
                        for( var i = 0; i<len; i++){
                            var id = response[i]['id'];
                            var code = response[i]['part_no'];
                            var name = response[i]['part_name'];
                            var slug = code+" "+name;
                            var path = "<?php echo asset('storage/parts/'); ?>";
                            var img = response[i]['img'];
                            var img_path = path+'/'+img;
                            if (img != null) {
                                $("#searchResult3").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'><div class='col-sm-11'><img src='"+img_path+"' width='60'>"+slug+"</div><div class='col-sm-1'><span class='badge bg-success rounded-pill'><span class='fa fa-arrow-right' aria-hidden='true'></span></span></div></li>");
                            }else{
                                $("#searchResult3").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'><div class='col-sm-11'>"+slug+"</div><div class='col-sm-1'><span class='badge bg-success rounded-pill'><span class='fa fa-arrow-right' aria-hidden='true'></span></span></div></li>");
                            }
                        }

                        // binding click event to li
                        $("#searchResult3 li").bind("click",function(){
                            addOrderTemp(this);
                        });

                    }
                })
            });

            $('.empty-search').on('click', function(){
                console.log('')
                $("#search_key").val('');
                $("#searchResult3").empty();
            });

            $('#btn-submit').on('click', function(e){
                e.preventDefault();
                var vehicle = document.getElementById('vehicle_id').value;
                if (vehicle == '') {
                    $('#ermsg').append('<div class="alert alert-danger hideit alertSuc">Please select a Vehicle</div >');
                    setTimeout(function() {
                        $('.hideit').fadeOut('slow', function() {
                            $(this).remove();
                        });
                    }, 1300);
                }else{
                    document.getElementById('stockform').submit();
                }
            })
        });

        function addOrderTemp(element) {
            var value = $(element).text();
            var partid = $(element).val();
            $('#part-id').val(partid);
            $('#part-form').submit(); 
        }
    </script> 

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
             // Save data
            $(".edit").focusout(function(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                // $(this).removeClass("editMode");
                var id = this.id;
                var split_id = id.split("_");
                var field_name = split_id[0];
                var edit_id = split_id[1];
                var value = $(this).val();

                $.ajax({
                    url: "{{ url('update-pu-item') }}",
                    type: 'POST',
                    data: { pu_qty: value, id:edit_id },
                    success:function(response){
                        if(response.success == 1){
                            $('#msg').append('<div class="alert alert-success hideit alertSuc">' + response.msg + '.</div >');
                            setTimeout(function() {
                                $('.hideit').fadeOut('slow', function() {
                                    $(this).remove();
                                });
                            }, 1300);
                        }else{
                            $('#msg').append('<div class="alert alert-danger hideit alertSuc">' + response.msg + '.</div >');
                            setTimeout(function() {
                                $('.hideit').fadeOut('slow', function() {
                                    $(this).remove();
                                    // location.reload();
                                    
                                });
                            }, 1300);
                        }
                    }
                });
            });
        });
    </script>

    <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
    <script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="pu_date"]');
        
            var mind = 60;
            var d = new Date();
            d.setDate(d.getDate() - mind);
            $min.DatePickerX.init({
                mondayFirst: true,
                minDate    : d,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>