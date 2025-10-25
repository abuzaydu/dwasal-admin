@extends('layouts.prod')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
<script type="text/javascript" src="../js/mrouse.js"></script>
<script>
    function validateform(form) {
        var items = document.mrousedform.no_items.value;
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
            window.location.href="{{url('cancel-mroused')}}";
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function weg(elem) {
      var x = document.getElementById("mroused_date_field");
      if(elem.value !== "auto") {
        x.style.display = "block";
      } else {
        x.style.display = "none";
        $("#mroused_date").val('');
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

</script>

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3"></div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row" ng-controller="SearchItemCtrl">
         <div class="col-xl-4 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.search_expense_type')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <div class="p-2 border rounded"> 
                         <div class="col-sm-12 text-center mb-3">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#mroModal">
                            <i class="fa fa-plus"></i>
                            {{trans('navmenu.new_type')}}
                            </button>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label">{{trans('navmenu.search_tap')}}</label> 
                            <input ng-model="searchKeyword" placeholder="{{trans('navmenu.search_expense_type')}}" class="form-control form-control-sm mb-3">
                        </div> 
                        <div class="col-sm-12">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center" ng-repeat="item in items  | filter: searchKeyword | limitTo:10" ng-click="addUsedMroTemp(item, newmrousedtemp)">
                                    <div class="col-sm-11">
                                       @{{item.name}}<br>
                                       <small style="color: #757575;">@{{item.description}}</small>
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
        <div class="col-xl-8 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.overhead_expenses')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">

                    <div class="p-4 border rounded">
                        <form class="row g-3" name="mrousedform" method="POST" action="{{route('mro-uses.store')}}" onsubmit="return validateform(this)">
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

                            <div class="col-sm-3" id="mroused_date_field" style="display: none;">
                                <div class="form-group" >
                                    <label class="form-label">{{trans('navmenu.date')}}</label>
                                    <div class="input-group date">
                                        <input type="text" name="mroused_date" id="mroused_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <table class="table table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: center;">{{trans('navmenu.expense_type')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                        <th style="text-align: center;">&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newmrousedtemp in mrousedtemp" id="temps">
                                        <td>@{{$index + 1}}</td>
                                        <td>@{{newmrousedtemp.name}}</td>
                                        <td><input type="number" name="quantity" ng-blur="updateMroTemp(newmrousedtemp)" ng-model="newmrousedtemp.quantity" min="0" step="any" value="@{{newmrousedtemp.quantity}}" style="text-align:center; width: 140px;" autocomplete="off" class="form-control form-control-sm"></td>
                                        <td><input type="number" name="unit_cost" ng-blur="updateMroTemp(newmrousedtemp)" ng-model="newmrousedtemp.unit_cost" min="0" step="any" value="@{{newmrousedtemp.unit_cost}}" style="text-align:center; width: 140px;" autocomplete="off" class="form-control form-control-sm"></td>
                                        <td style="text-align:center;">@{{newmrousedtemp.total | number:0}}</td>
                                        <td><a href="#" ng-click="removeMroTemp(newmrousedtemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                       <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                       <th style="text-align: center;"><b>@{{sum(mrousedtemp) | number:2}}</b></th>
                                       <th></th>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-xl-6">
                                <button type="submit" name="myButton" class="btn btn-success btn-sm mb-3">{{trans('navmenu.btn_submit')}}</button>
                                <button onclick="confirmCancel()" type="button" class="btn btn-warning btn-sm mb-3">{{trans('navmenu.btn_cancel')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->

<div class="modal fade" id="mroModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">{{trans('navmenu.new_type')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form" method="POST" action="{{route('mro.store')}}">
                @csrf
                <div class="row ms-10">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="form-label">{{trans('navmenu.expense_type')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.mro_name')}}" class="form-control form-control-sm mb-4">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="float-start">
                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>

@endsection
<link rel="stylesheet" href="../css/DatePickerX.css">

<script src="../js/DatePickerX.min.js"></script>
<script>
    window.addEventListener('DOMContentLoaded', function()
    {
        var $min = document.querySelector('[name="mroused_date"]');
        $min.DatePickerX.init({
            mondayFirst: true,
            format     : 'yyyy-mm-dd',
            maxDate    : new Date()
        });

    });
</script>