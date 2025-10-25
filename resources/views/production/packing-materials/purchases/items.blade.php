@extends('layouts.prod')
<script type="text/javascript">
    function weg(elem) {
      var x = document.getElementById("date_field");
      if(elem.value !== "auto") {
        x.style.display = "block";
      } else {
        x.style.display = "none";
        $("#datepicker").val('');
      }
    }

    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_delete')}}",
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
              "Your Product has been deleted.",
              'success'
            )
          }
        })
    }

    function confirmDeletePayment(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_delete')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('delete-form-pay-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "Your Product has been deleted.",
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
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-7 col-sm-12 text-right">
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-12 mx-auto">
            <div  class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
                <div class="col">
                    <div class="card radius-10 ">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">{{trans('navmenu.supplier')}}</p>
                                    <h4 class="my-1">@if(!is_null($supplier)){{$supplier->name}}@else {{trans('navmenu.unknown')}} @endif</h4>
                                </div>
                                <div class="widgets-icons bg-light-primary text-primary ms-auto"><i class="bx bxs-box"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col">
                    <div class="card radius-10 ">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">{{trans('navmenu.total_amount')}}</p>
                                    <h4 class="my-1">{{number_format($purchase->total_amount)}}</h4>
                                </div>
                                <div class="widgets-icons bg-light-warning text-warning ms-auto"><i class="fa fa-money"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col">
                    <div class="card radius-10 ">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">{{trans('navmenu.amount_paid')}}</p>
                                    <h4 class="my-1">{{number_format($purchase->amount_paid)}}</h4>
                                </div>
                                <div class="widgets-icons bg-light-info text-info ms-auto"><i class="fa fa-money"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col">
                    <div class="card radius-10 ">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">{{trans('navmenu.unpaid')}}</p>
                                    <h4 class="my-1">{{number_format($purchase->total_amount-$purchase->amount_paid)}}</h4>
                                </div>
                                <div class="widgets-icons bg-light-danger text-danger ms-auto"><i class="fa fa-money"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col -->
            </div>

              <!-- =========================================================== -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card radius-10">
                        <div class="card-header">
                            <h5>Purchased Items</h5>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <th>#</th>
                                    <th>{{trans('navmenu.material_name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                    <th style="text-align: center;">UOM</th>
                                    <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.purchase_date')}}</th>
                                    <th>{{trans('navmenu.actions')}}</th>
                                </thead>
                                <tbody>
                                    @foreach($pitems as $index => $rmitem)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$rmitem->name}}</td>
                                        <td style="text-align: center;">{{number_format($rmitem->qty)}}</td>
                                        <td> <span style="color: gray;">{{$rmitem->basic_uom}}</span></td>
                                        <td style="text-align: center;">{{number_format($rmitem->unit_cost)}}</td>
                                        <td style="text-align: center;">{{number_format($rmitem->unit_cost*$rmitem->qty)}}</td>
                                        <td style="text-align: center;">{{$rmitem->date}}</td>
                                        <td style="text-align: center;">
                                            <a href="{{route('pm-items.edit', encrypt($rmitem->id))}}">
                                                <i class="fa fa-edit" style="color: blue;"></i>
                                            </a>|
                                            <form id="delete-form-{{$index}}" method="POST" action="{{route('pm-items.destroy', encrypt($rmitem->id))}}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" onclick="confirmDelete('{{$index}}')">
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

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{trans('navmenu.purchase_payments')}}</h5>
                        </div>
                        <div class="card-body">
                            @csrf
                            <table id="del-multiple" class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{trans('navmenu.pay_date')}}</th>
                                        <th>{{trans('navmenu.amount')}}</th>
                                        <th>{{trans('navmenu.account')}}</th>
                                        <th>{{trans('navmenu.record_at')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $index => $payment)
                                    <tr>
                                        <td>{{$payment->id}}</td>
                                        <td>{{$payment->pay_date}}</td>
                                        <td>{{number_format($payment->amount)}}</td>
                                        <td>
                                            @if($payment->account == 'Cash')
                                              @if(app()->getLocale() == 'en')
                                                {{$payment->account}}
                                              @else
                                              {{trans('navmenu.cash')}}
                                              @endif
                                            @elseif($payment->account == 'Mobile Money')
                                              @if(app()->getLocale() == 'en')
                                                {{$payment->account}}
                                              @else
                                                {{trans('navmenu.mobilemoney')}}
                                              @endif
                                            @elseif($payment->account == 'Bank')
                                              @if(app()->getLocale() == 'en')
                                                {{$payment->account}}
                                              @else
                                                {{trans('navmenu.bank')}}
                                              @endif                           
                                            @endif
                                        </td>
                                        <td>{{$payment->created_at}}</td>
                                        <td>
                                            <a href="{{ route('pm-purchase-payments.edit', encrypt($payment->id))}}">
                                                <i class="fa fa-edit" style="color: blue;"></i>
                                            </a>
                                            |
                                            <form id="delete-form-pay-{{$index}}" method="POST" action="{{route('pm-purchase-payments.destroy', encrypt($payment->id))}}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" onclick="confirmDeletePayment('{{$index}}')">
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
            var $min = document.querySelector('[name="mnf_date"]'),
                $max = document.querySelector('[name="exp_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    :  new Date(),
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : $min,
                // maxDate    : new Date()
            });

        });
    </script>