@extends('layouts.prod')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
<script type="text/javascript" src="../js/pmuse.js"></script>
<script> 
    function validateform(form) { 
        var items = document.pmusedform.no_items.value;
        if (items == 0) {
            // alert('Please select at least one item to continue.');
            Swal.fire(
              'Nothing To Submit!',
              'Please select at least one item to continue.',
              'info'
            )
            return false;
        }

        form.myButton.disabled = true;
        form.myButton.value = "Please wait...";
        return true;
        
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
            window.location.href="{{url('cancel-pmused')}}";
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function weg(elem) {
          var x = document.getElementById("pmused_date_field");
          if(elem.value !== "auto") {
            x.style.display = "block";
          } else {
            x.style.display = "none";
            $("#pmused_date").val('');
          }
    }

   function batch(elem) {
          var b = document.getElementById("prod_batch");
      
          if(elem.value !== "auto") {
            b.readOnly = false; 
          } else {
            b.readOnly = true;
          }
    }

    function saveprodtemp(elem){
      var tempid = elem.getAttribute("data-temp");
      var product_id = elem.options[elem.selectedIndex].value;

         $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
          });

        $.ajax({ 
          method: "POST",
          url: 'api/saveprodtemp',  
          data: {tempid: tempid , product_id : product_id},
          type: 'json',
          success: function(data){
           console.log(data);
          }
        });
    }
</script>

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3"></div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{url('prod-home')}}"><i class="fa fa-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row" ng-controller="SearchItemCtrl">
        <div class="col-xl-3 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.search_packing_material')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <div class="p-2 border rounded"> 
                        <div class="col-sm-12">
                            <label class="form-label">{{trans('navmenu.search_tap')}}</label> 
                            <input ng-model="searchKeyword" placeholder="{{trans('navmenu.search_packing_material')}}" class="form-control form-control-sm mb-3">
                        </div> 
                        <div class="col-sm-12">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center" ng-repeat="item in items  | filter: searchKeyword | limitTo:10" ng-click="addStockTemp(item, newpmusedtemp)">
                                    <div class="col-sm-11">
                                        @{{item.name}}
                                        <span style="color: blue;" ng-if="item.in_store > 0">(@{{item.in_store}})</span>
                                        <span style="color: red;" ng-if="item.in_store == 0">(@{{item.in_store}})</span>
                                    </div>
                                    <div class="col-sm-1">
                                        <span class="badge bg-success rounded-pill"><span class="fa fa-arrow-right" aria-hidden="true"></span></span>
                                    </div>
                                </li>
                            </ul>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-9 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.new_use_of_pm')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">

                    <div class="p-4 border rounded">
                        <form class="row g-3"  class="form" name="rmusedform" method="POST" action="{{route('pm-uses.store')}}" onsubmit="return validateform(this)">
                            @csrf
                            <div class="col-sm-3">
                                <div class="form-group">
                                     <label for="batch_no_set" class="form-label">{{trans('navmenu.batch_no')}}</label>
                                    <select name="batch_no_set" id="batch_no_set" onchange="batch(this)" class="form-control form-control-sm mb-3">
                                        <option value="auto">Auto</option>
                                        <option value="manaul">Manual</option>
                                    </select>
                                </div>
                            </div>
                             <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="prod_batch" class="form-label">{{trans('navmenu.batch_no')}}</label>
                                    <input type="text" name="prod_batch" class="form-control form-control-sm mb-3" id="prod_batch" value="{{$prod_batch}}" readonly/>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <label for="date_set" class="form-label">{{trans('navmenu.date')}}</label>
                                <select name="date_set" id="date_set" onchange="weg(this)" class="form-select form-select-sm mb-3">
                                    <option value="auto">Auto</option>
                                    <option value="manaul">Manual</option>
                                </select>
                            </div>
                            <div class="col-sm-3" id="pmused_date_field" style="display: none;">
                                <label class="form-label">{{trans('navmenu.date')}}</label>
                                <div class="date">
                                    <input type="text" name="pmused_date" id="pmused_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-select-sm mb-3" readonly/>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <table class="table table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: center;">{{trans('navmenu.packing_name')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.product_packed')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.unit_packed')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                        <th style="text-align: center;">&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newpmusedtemp in pmusedtemp" id="temps">
                                        <td>@{{$index + 1}}</td>
                                        <td>@{{newpmusedtemp.name}}</td>
                                        <td><input type="number" name="quantity" ng-blur="updateStockTemp(newpmusedtemp)" ng-model="newpmusedtemp.quantity" min="0" step="any" value="@{{newpmusedtemp.quantity}}" style="text-align:center;" autocomplete="off" class="form-control form-control-sm"></td>
                                        <td>
                                             <select id="prod_packed@{{$index + 1}}" name ="product_packed" class="form-select form-select-sm my_select" ng-model="newpmusedtemp.product_packed" ng-change="updateStockTemp(newpmusedtemp)" style="width: 200px;"

                                             ng-options="product.id as product.name for product in prod_list">
                                            </select>
                                        </td>
                                        <td><input type="number" name="unit_packed" ng-blur="updateStockTemp(newpmusedtemp)" ng-model="newpmusedtemp.unit_packed" min="0" step="any" value="@{{newpmusedtemp.unit_packed}}" style="text-align:center;" autocomplete="off" class="form-control form-control-sm"></td>
                                        <td style="text-align:center;">@{{newpmusedtemp.unit_cost | number:0}}</td>
                                        <td style="text-align:center;">@{{newpmusedtemp.total | number:0}}</td>
                                        <td><a href="#" ng-click="removeStockTemp(newpmusedtemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align: center;">{{trans('navmenu.total')}}</td>
                                        <td style="text-align: center;"><p class="form-control-static"><b>@{{sum(pmusedtemp) | number:0}}</b></p></td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <label for="comments" class="form-label">{{trans('navmenu.comments')}}</label>
                                            <textarea  class="form-control form-control-sm mb-3" name="comments" id="comments" ></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 pt-4">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <button type="submit" name="myButton" class="btn btn-success btn-block mb-3">{{trans('navmenu.btn_submit')}}</button>
                                        </div>
                                        <div class="col-sm-4">
                                            <button onclick="confirmCancel()" type="button" class="btn btn-warning btn-block mb-3">{{trans('navmenu.btn_cancel')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->

@endsection
<link rel="stylesheet" href="../css/DatePickerX.css">

<script src="../js/DatePickerX.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="pmused_date"]'),
                $max = document.querySelector('[name="due_date"]');


            $min.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : new Date(),
                // maxDate    : new Date()
            });

        });
    </script>