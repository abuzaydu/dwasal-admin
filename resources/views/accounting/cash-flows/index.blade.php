@extends('layouts.acc')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
<script type="text/javascript">
    function confirmDelete(id) {
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
            document.getElementById('delete-form-cashout-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function confirmDeleteCashin(id) {
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
            document.getElementById('delete-form-cashin-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }


    function confirmDeleteTrans(id) {
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
            document.getElementById('delete-form-' + id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function wegBorrow(elem) {
        var x = document.getElementById("sel_customer");
        if(elem.value !== "No") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    }

    function weg(elem) {
        var x = document.getElementById("date_field");
        if(elem.value !== "auto") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
            $("#out_date").val('');
        }
    }

    function wegIn(elem) {
        var x = document.getElementById("indate_field");
        if(elem.value !== "auto") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
            $("#in_date").val('');
        }
    }

    function wegactx(elem) {
        var x = document.getElementById("tx_date_field");
        if(elem.value !== "auto") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
            $("#date").val('');
        }
    }
</script>

@section('content')
    
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Accounting</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-9 col-md-9 col-sm-12 text-right">
                <form class="dashform row g-3" action="{{ url('filter-cash-flows')}}" method="POST">
                    @csrf
                    <div class="col-sm-5">
                        <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#actxModal" data-backdrop="static" data-keyboard="false"><i class="fa fa-plus-circle"></i>{{trans('navmenu.new_transaction')}}</a>
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="col-sm-7">
                        <div class="input-group">
                            <button type="button" class="btn btn-white btn-sm float-end" id="reportrange">
                                <span><i class="fa fa-calendar"></i></span>
                                <i class="fa fa-caret-down"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                 <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2 nav-success" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#transactions" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.accounts_transactions')}}</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#cash-ins" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.cash_inflow')}}</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#cash-outs" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.cash_outflow')}}</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="transactions" role="tabpanel">
                            <table id="example2" class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <!-- <th></th> -->
                                        <th>{{trans('navmenu.date')}}</th>
                                        <th>{{trans('navmenu.from')}}</th>
                                        <th>{{trans('navmenu.to')}}</th>
                                        <th>{{trans('navmenu.amount')}}</th>
                                        <th>{{trans('navmenu.reason')}}</th>
                                        <th>{{trans('navmenu.created_at')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($acctransactions as $index => $trans)
                                    <?php 
                                        $from_acc = App\Models\Account::find($trans->from_acc_id);
                                        $from = $from_acc->account_name;
                                        if (!is_null($from_acc->account_number)) {
                                            $from = $from_acc->account_name.'('.$from_acc->account_number.')';
                                        }

                                        $to_acc = App\Models\Account::find($trans->to_acc_id);
                                        $to = $to_acc->account_name;
                                        if (!is_null($to_acc->account_number)) {
                                            $to = $to_acc->account_name.'('.$to_acc->account_number.')';
                                        }
                                    ?>
                                    <tr>
                                        <!-- <td>{{$trans->id}}</td> -->
                                        <td>{{$trans->date}}</td>
                                        <td>{{$from}}</td>
                                        <td>{{$to}}</td>
                                        <td>{{number_format($trans->amount, 2, '.', ',')}}</td>
                                        <td>{{$trans->reason}}</td>
                                        <td>{{$trans->created_at}}</td>
                                        <td>
                                            @if(Auth::user()->can('edit-cash-flow'))
                                            <a href="{{route('acc-transactions.edit', encrypt($trans->id))}}">
                                                <i class="fa fa-edit" style="color: blue;"></i>
                                            </a>
                                            @endif
                                            @if(Auth::user()->can('delete-cash-flow')) |
                                            <form id="delete-form-{{$index}}" action="{{route('acc-transactions.destroy' , encrypt($trans->id) )}}" method="POST" style="display: inline-block;">
                                                @method('DELETE')
                                                 @csrf
                                                <a href="#" class="button" onclick="confirmDeleteTrans('{{$index}}')"><i class="fa fa-trash" style="color: red;"></i></a>
                                            </form>      
                                             @endif   
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="cash-ins" role="tabpanel">
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs nav-tabs-new py-3">
                                    <li class="nav-item"><a class="nav-link" href="#tab_1-in" data-bs-toggle="tab">{{trans('navmenu.cash_from_sales')}}</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#tab_2-in" data-bs-toggle="tab" onclick="return showInBtn()">{{trans('navmenu.other_source')}}</a></li>
                                    <a href="#" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#cinModal" data-backdrop="static" data-keyboard="false"><i class="fa fa-plus-circle"></i>{{trans('navmenu.new_cash_in')}}</a>
                                </ul>
                                <div class="tab-content ">
                                    <div class="tab-pane active" id="tab_1-in">  
                                        <table id="example4" class="table table-striped display nowrap" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>{{trans('navmenu.date')}}</th>
                                                    <th>{{trans('navmenu.account')}}</th>
                                                    <th>{{trans('navmenu.amount')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $total_sales = 0; ?>
                                                @foreach($salescashins as $index => $scin)
                                                <?php $total_sales += $scin->amount; ?>
                                                <tr>
                                                    <td>{{$scin->pay_date}}</td>
                                                    <td>
                                                       @if($scin->pay_mode == 'Cash')
                                                         @if(app()->getLocale() == 'en')
                                                           {{$scin->pay_mode}}
                                                         @else
                                                         {{trans('navmenu.cash')}}
                                                       @endif
                                                       @elseif($scin->pay_mode == 'Mobile Money')
                                                         @if(app()->getLocale() == 'en')
                                                           {{$scin->pay_mode}}
                                                         @else
                                                           {{trans('navmenu.mobilemoney')}}
                                                         @endif
                                                       @elseif($scin->pay_mode == 'Bank')
                                                         @if(app()->getLocale() == 'en')
                                                           {{$scin->pay_mode}}
                                                         @else
                                                           {{trans('navmenu.bank')}}
                                                         @endif                           
                                                       @endif
                                                    </td>
                                                    <td>{{number_format($scin->amount, 2,'.', ',')}}</td>   
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th></th>
                                                    <th>{{trans('navmenu.total')}}</th>
                                                    <th>{{number_format($total_sales)}}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="tab-pane" id="tab_2-in">
                                        <table id="example6" class="table table-striped display nowrap" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>{{trans('navmenu.date')}}</th>
                                                    <th>{{trans('navmenu.account')}}</th>
                                                    <th>{{trans('navmenu.amount')}}</th>
                                                    <th>{{trans('navmenu.source')}}</th>
                                                    <th>{{trans('navmenu.created_at')}}</th>
                                                    <th>{{trans('navmenu.actions')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($cashins as $index => $cin)
                                                <?php $acc = App\Models\Account::find($cin->account_id);
                                                    $account_in = $acc->account_name;
                                                    if (!is_null($acc->account_number)) {
                                                        $account_in = $acc->account_name.'('.$acc->account_number.')';
                                                    }
                                                ?>
                                                <tr>
                                                    <td>{{$cin->in_date}}</td>
                                                    <td>{{$account_in}}</td>
                                                    <td>{{number_format($cin->amount, 2,'.', ',')}}</td>
                                                    <td>{{$cin->source}}</td>
                                                    <td>{{$cin->created_at}}</td>
                                                    <td>
                                                        @if(Auth::user()->can('edit-cash-flow'))
                                                        <a href="{{route('cash-ins.edit', encrypt($cin->id))}}">
                                                            <i class="fa fa-edit" style="color: blue;"></i>
                                                        </a>
                                                        @endif
                                                        @if(Auth::user()->can('delete-cash-flow'))
                                                        <form id="delete-form-cashin-{{$index}}" method="POST" action="{{route('cash-ins.destroy' , encrypt($cin->id) )}}" style="display: inline;">
                                                            @method('DELETE')
                                                            @csrf
                                                            <a href="#" class="button" onclick="confirmDeleteCashin('{{$index}}')"><i class="fa fa-trash" style="color: red;"></i></a>
                                                        </form>
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
                        <div class="tab-pane fade" id="cash-outs" role="tabpanel">
                            <div class="d-flex   px-1 py-3">
                                <ul class="nav nav-tabs nav-tabs-new">
                                    <li class="nav-item"><a class="nav-link" href="#tab_1" data-bs-toggle="tab">{{trans('navmenu.operating_expense')}}</a></li>
                                    <li class="nav-item"><a  class="nav-link" href="#tab_2" data-bs-toggle="tab">{{trans('navmenu.stock_purchasing')}}</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#tab_3" data-bs-toggle="tab" onclick="return showOutBtn()">{{trans('navmenu.others')}}</a></li>
                                    <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#coutModal" data-backdrop="static" data-keyboard="false"><i class="fa fa-plus-circle"></i>{{trans('navmenu.new_cash_out')}}</a>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab_1">
                                    <table id="example1" class="table table-striped display nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <!-- <th></th> -->
                                                <th>{{trans('navmenu.date')}}</th>
                                                <th>{{trans('navmenu.amount')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $pout_total = 0; ?>
                                            @foreach($expcashouts as $index => $expout)
                                            <?php $pout_total += $expout->amount; ?>
                                            <tr>
                                                <td>{{$expout->pay_date}}</td>
                                                <td>{{number_format($expout->amount, 2, '.', ',')}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <!-- <th></th> -->
                                                <th>{{trans('navmenu.total')}}</th>
                                                <th>{{number_format($pout_total, 2, '.', ',')}}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="tab_2">
                                    <table id="example5" class="table table-striped display nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <!-- <th></th> -->
                                                <th>{{trans('navmenu.date')}}</th>
                                                <th>{{trans('navmenu.amount')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $pout_total = 0; ?>
                                            @foreach($purchcashouts as $index => $pout)
                                            <?php $pout_total += $pout->amount; ?>
                                            <tr>
                                                <td>{{$pout->pay_date}}</td>
                                                <td>{{number_format($pout->amount, 2,'.', ',')}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <!-- <th></th> -->
                                                <th>{{trans('navmenu.total')}}</th>
                                                <th>{{number_format($pout_total, 2,'.', ',')}}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="tab_3">
                                    <table id="example3" class="table table-striped display nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <!-- <th></th> -->
                                                <th>{{trans('navmenu.date')}}</th>
                                                <th>{{trans('navmenu.account')}}</th>
                                                <th>{{trans('navmenu.amount')}}</th>
                                                <th>{{trans('navmenu.reason')}}</th>
                                                <th>{{trans('navmenu.created_at')}}</th>
                                                <th>{{trans('navmenu.actions')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $tcout = 0; ?>
                                            @foreach($cashouts as $index => $cout)
                                            <?php $tcout += $cout->amount; 
                                                $acc = App\Models\Account::find($cout->account_id);
                                                $account_out = $acc->account_name;
                                                if (!is_null($acc->account_number)) {
                                                    $account_out = $acc->account_name.'('.$acc->account_number.')';
                                                }
                                            ?>
                                            <tr>
                                                <!-- <td>{{$cout->id}}</td> -->
                                                <td>{{$cout->out_date}}</td>
                                                <td>{{$account_out}}</td>
                                                <td>{{number_format($cout->amount,2, '.', ',')}}</td>
                                                <td>{{$cout->reason}}</td>
                                                <td>{{$cout->created_at}}</td>
                                                <td>
                                                    <a href="{{route('cash-flows.show', encrypt($cout->id))}}">
                                                        <i class="fa fa-eye" style="color: blue;"></i>
                                                    </a>
                                                    @if(Auth::user()->can('edit-cash-flow'))
                                                    <a href="{{route('cash-flows.edit', encrypt($cout->id))}}">
                                                        <i class="fa fa-edit" style="color: blue;"></i>
                                                    </a>
                                                    @endif
                                                    @if(Auth::user()->can('delete-cash-flow'))
                                                    <form id="delete-form-cashout-{{$index}}" method="POST" action="{{route('cash-flows.destroy' , encrypt($cout->id) )}}" style="display: inline;">
                                                        @method('DELETE')
                                                        @csrf
                                                        <a href="#" class="button" onclick="confirmDelete('{{$index}}')"><i class="fa fa-trash" style="color: red;"></i></a>
                                                    </form>
                                                    @endif
                                                   </td>
                                               </tr>
                                               @endforeach
                                           </tbody>
                                           <tfoot>
                                               <tr>
                                                   <!-- <th></th> -->
                                                   <th></th>
                                                   <th>{{trans('navmenu.total')}}</th>
                                                   <th>{{ number_format($tcout)}}</th>
                                                   <th></th>
                                                   <th></th>
                                                   <th></th>
                                               </tr>
                                           </tfoot>
                                       </table>
                                   </div>
                                   <!-- /.tab-pane -->
                               </div>
                           </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
<div class="modal fade" id="coutModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">  
                <h6 class="modal-title" id="myModalLabel">{{trans('navmenu.new_cash_out')}}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <form class="my-form" method="POST" action="{{route('cash-flows.store')}}">
                    <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.is_borrowed')}}</label>
                            <select onchange="wegBorrow(this)" name="is_borrowed" class="form-select form-select-sm mb-1">
                                <option>No</option>
                                <option>Yes</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="sel_customer" style="display: none;">
                            <label class="form-label">{{trans('navmenu.customer')}}</label>
                            <select name="customer_id" class="form-select form-select-sm">
                                <option value="">{{trans('navmenu.select_customer')}}</option>
                                @foreach($customers as $customer)
                                <option value="{{$customer->id}}">{{$customer->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Activity Category</label>
                            <select class="form-select form-select-sm mb-1" name="category" required>
                                <option value="">Select Category</option>
                                <option>Investing Activities</option>
                                <option>Financing Activities</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="register-username" class="label-control">Reason <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="register-username" type="text" name="reason" required placeholder="Please enter Reason" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Account <span style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-1" name="account_id" required style="width: 100%;">
                                <option value="">Select Account</option>
                                @foreach($accounts as $acc)
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Amount <span style="color: red; font-weight: bold;">*</span></label>
                            <input type="number" step="any" name="amount" placeholder="Please enter Amount" class="form-control form-control-sm mb-1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.pick_date')}}</label>
                            <div class="inner-addon left-addon">
                                <i class="myaddon fa fa-calendar"></i>
                                <input type="text" name="out_date" id="out_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                            </div> 
                        </div>  
                    </div>                
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-sm" id="btn-submit">Save</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
    </div>
</div>

     <!-- Modal -->
<div class="modal fade" id="cinModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="myModalLabel">{{trans('navmenu.new_cash_in')}}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <form class="my-form" method="POST" action="{{route('cash-ins.store')}}">
                    <div class="modal-body row">
                    @csrf
                        <div class="col-md-6">
                            <label class="form-label">Activity Category</label>
                            <select class="form-select form-select-sm mb-1" name="category" required>
                                <option value="">Select Category</option>
                                <option>Investing Activities</option>
                                <option>Financing Activities</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="register-username" class="form-label">{{trans('navmenu.source')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="register-username" type="text" name="source" required placeholder="Please enter source of this fund" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Account <span style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-1" name="account_id" required style="width: 100%;">
                                <option value="">Select Account</option>
                                @foreach($accounts as $acc)
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Amount <span style="color: red; font-weight: bold;">*</span></label>
                            <input type="number" step="any" name="amount" placeholder="Please enter Amount" class="form-control form-control-sm mb-1" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.pick_date')}}</label>
                            <div class="inner-addon left-addon">
                                <i class="myaddon fa fa-calendar"></i>
                                <input type="text" name="in_date" id="in_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                            </div> 
                        </div>                
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-sm" id="btn-submit">Save</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="actxModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="myModalLabel">{{trans('navmenu.new_transaction')}}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <form class="my-form" method="POST" action="{{route('acc-transactions.store')}}">
                    <div class="modal-body">
                    @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">{{trans('navmenu.from')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-select form-select-sm mb-1" name="from" required style="width: 100%;">
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $acc)
                                    <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">{{trans('navmenu.to')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-select form-select-sm mb-1" name="to" required style="width: 100%;">
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $acc)
                                    <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Amount <span style="color: red; font-weight: bold;">*</span></label>
                                <input type="number" name="amount" placeholder="Please enter Amount" class="form-control form-control-sm mb-1" required autocomplete="off"> 
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{trans('navmenu.pick_date')}}</label>
                                <div class="inner-addon left-addon">
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="date" id="date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="register-username" class="label-control">Reason  <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="register-username" type="text" name="reason" required placeholder="Please enter Reason(Optional)" class="form-control form-control-sm mb-1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-sm" id="btn-submit">Save</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
    </div>
</div>
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    
    <script>
        $(function () {
            //Exportable table
            $('#example1').DataTable();
            $('#example2').DataTable();
            $('#example3').DataTable();
            $('#example4').DataTable();
            $('#example5').DataTable();
            $('#example6').DataTable();

            
        });
    </script>
@endsection
<link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">

<script src="{{asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="out_date"]'),
                // $cfdate = document.querySelector('[name="cf_date"]'),
                $indate = document.querySelector('[name="in_date"]'),
                $max = document.querySelector('[name="date"]');


            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            // $cfdate.DatePickerX.init({
            //     mondayFirst: true,
            //     // minDate    : new Date(),
            //     format     : 'yyyy-mm-dd',
            //     maxDate    : new Date()
            // });

            $indate.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                // minDate    : new Date(),
                maxDate    : new Date()
            });

        });
    </script>