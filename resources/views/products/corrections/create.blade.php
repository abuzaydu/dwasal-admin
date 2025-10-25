@extends('layouts.inv')

<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
<script type="text/javascript" src="{{asset('js/stockcorrection.js')}}"></script>
<script type="text/javascript">
    
    function weg(elem) {
      var x = document.getElementById("date_field");
      if(elem.value !== "auto") {
        x.style.display = "block";
      } else {
        x.style.display = "none";
        $("#sale_date").val('');
      } 
    }

    function confirmCancel() {
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
            window.location.href="{{url('cancel-correction')}}";
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
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix" id="mycontroller" ng-controller="SearchItemCtrl">
        <div class="col-xl-12 mx-auto ">
            <div class=" card radius-6">
                <!-- /.box-header -->
                <div class="card-body">
                    <div class="p-4 border rounded" style="overflow: auto;">
                        <form class="row g-3 needs-validation" novalidate name="orderform" method="POST" action="{{route('stock-corrections.store')}}" onsubmit="return validateform(this)">
                            @csrf
                            <div class="p-2 border rounded print_invoice row g-1">
                                <input type="hidden" name="destin_id" value="{{$shop->id}}">
                                <div class="col-sm-3">
                                    <label for="order_date" class="form-label">{{trans('navmenu.pick_date')}} <span style="color: red; font-weight: bold;">*</span></label>
                                    <div class="inner-addon left-addon">
                                        <i class="myaddon fa fa-calendar"></i> 
                                        <input type="text" name="order_date" id="datepicker" class="form-control form-control-sm mb-1" placeholder="{{trans('navmenu.pick_date')}}"  aria-describedby="calendar">
                                    </div>
                                </div>
                                <div class="col-sm-9">
                                    <label class="form-label">Reason</label>
                                    <input type="text" name="reason" class="form-control form-control-sm mb-1" placeholder="Enter reason for correction">
                                </div>
                                <div class="col-sm-8">
                                    <label class="form-label">{{trans('navmenu.search_tap')}}</label>
                                    <div class="input-group mb-0">
                                        <input type="text" class="form-control form-control-sm mb-1" id="search_key" placeholder="{{trans('navmenu.search_product')}}" autocomplete="off" aria-label="Recipient's username" aria-describedby="button-addon2">
                                        <a class="btn btn-outline-danger btn-sm mb-1 empty-search" id="button-addon2"><i class='fa fa-close'></i></a>
                                    </div>
                                    <ul id="searchResult3" class="list-group"></ul>
                                </div>
                                <div class="col-md-12" style="border-top: 1px solid  #cdd0d4; padding-top: 5px;">
                                    <table class="items mt-0" style="width: 100%;">
                                        <tr>
                                            <th style="text-align: center;">#</th>
                                            <th>{{trans('navmenu.product_name')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                            <th style="text-align: center;">&nbsp;</th>
                                        </tr>
                                        <tr ng-repeat="newcorrectiontemp in correctiontemp" id="temps">
                                            <td style="text-align: center;">@{{$index + 1}}</td>
                                            <td>@{{newcorrectiontemp.name}}</td>
                                            <td style="text-align: center;"><input type="number" name="correction_qty" ng-blur="updateCorrectionTemp(newcorrectiontemp)" string-to-number ng-model="newcorrectiontemp.correction_qty" min="0" step="any" value="@{{newcorrectiontemp.correction_qty}}" style="text-align:center; height: 20px; width: 80px; border: 1px solid #e0e0e0;" autocomplete="off"></td>
                                            <td><a href="#" ng-click="removeCorrectionTemp(newcorrectiontemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-12 mt-2">  
                                    <button type="submit" class="btn btn-success btn-sm mb-1"><i class="fa fa-plus" ></i>{{trans('navmenu.btn_submit')}}
                                        </button>
                                    <a onclick="confirmCancel()" class="btn btn-warning btn-sm mr-1 card-subtitle" id="btn-cancel"><i class="fa fa-x"></i>{{trans('navmenu.btn_cancel')}}</a>
                                </div>
                            </div>
                        </form> 
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
            //     e.preventDefault();
            $('#search_key').on('keyup',function () {
                var query = $('#search_key').val();
                $.ajax({
                    url:"{{ url('search-product') }}",
                    type:'GET',
                    data:{'search_key':query},
                    success:function (response) {
                        // $('#product_list').html(data);
                        var len = response.length;
                        $("#searchResult3").empty();
                        for( var i = 0; i<len; i++){
                            var id = response[i]['id'];
                            var name = response[i]['name'];
                            $("#searchResult3").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'>"+name+"<span class='badge bg-success rounded-pill'> <i class='fa fa-arrow-right' aria-hidden='true'></i></span></li>");
                        }

                        // binding click event to li
                        $("#searchResult3 li").bind("click",function(){
                            addcorrectionTemp(this);
                        });

                    }
                })
            });

            $('.empty-search').on('click', function(){
                $("#search_key").val('');
                $("#searchResult3").empty();
            });
        });

        function addcorrectionTemp(element) {
            var value = $(element).text();
            var productid = $(element).val();
            $.ajax({
                url:"{{ url('fetch-product') }}",
                type:'GET',
                data:{'product_id':productid},
                success:function (response) {
                    var item = response;
                    angular.element(document.getElementById('mycontroller')).scope().addCorrectionTemp(item);
                    setTimeout(function(){
                        $("#search_key").val('');
                        $("#searchResult3").empty();
                    }, 2000);
                }
            })   
        }
    </script>
    <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
    <script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="order_date"]')

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>