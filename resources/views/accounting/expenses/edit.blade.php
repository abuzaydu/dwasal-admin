@extends('layouts.acc')
    <script>
        function showfield(elem) {
            var unit = document.getElementById('by-unit-cost');
            var amt = document.getElementById('by-total-amt');
            if (elem.value == 1) {
                amt.style.display = 'block';
                unit.style.display = 'none';
            }else{
                unit.style.display = 'block';
                amt.style.display = 'none';
            }
        }
    </script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Accounting</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-3 col-sm-12 text-right">
            
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('expenses.update', encrypt($expense->id)) }}">
                        @csrf
                        @method('PATCH')
                        <div class="row g-1 align-items-center">
                            <div class="col-sm-3">
                                <label class="form-label">{{ trans('navmenu.expense_date') }}</label>
                                <div class="inner-addon left-addon">
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="expense_date" id="expense_date" placeholder="{{ trans('navmenu.pick_date') }}" value="{{$expense->time_created}}" class="form-control form-control-sm mb-1">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="register-username" class="form-label">{{ trans('navmenu.expense_type') }} <span style="color: red;">*</span></label>
                                <select name="expense_item_id" required class="form-select form-select-sm mb-1">
                                    @foreach($expitems as $expitem)
                                    @if($expense->expense_item_id == $expitem->id)
                                    <option value="{{$expitem->id}}" selected>{{$expitem->expense_type}}</option>
                                    @else
                                    <option value="{{$expitem->id}}">{{$expitem->expense_type}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md-3">
                                <label for="qty" class="form-label">{{ trans('navmenu.qty') }}</label>
                                <input type="number" name="qty" class="form-control form-control-sm mb-1" min="0" step="any" placeholder="Enter quantity" value="{{$expense->qty}}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Update Amount By <span style="color: red;">*</span></label>
                                <select name="update_by" onchange="showfield(this)" class="form-select form-select-sm mb-1">
                                    <option value="1">Total Amount</option>
                                    <option value="0">Unit Cost</option>
                                </select>
                            </div>
                            <div class="col-md-3" id="by-unit-cost" style="display: none;">
                                <label class="form-label">{{ trans('navmenu.unit_cost') }} <span style="color: red;">*</span></label>
                                <input type="number" name="unit_cost" placeholder="{{ trans('navmenu.hnt_amount') }}" class="form-control form-control-sm mb-1" value="{{$expense->unit_cost}}">
                            </div>
                            <div class="col-md-3" id="by-total-amt">
                                <label class="form-label">{{ trans('navmenu.amount') }} <span style="color: red;">*</span></label>
                                <input type="number" name="amount" placeholder="{{ trans('navmenu.hnt_amount') }}" class="form-control form-control-sm mb-1" value="{{$expense->amount}}">
                            </div>
                            <!-- <div class="col-md-3">
                                <label class="form-label">Expense Category</label>
                                <select name="expense_category_id" class="form-select form-select-sm mb-1">
                                    <option value="">None</option>
                                    @foreach ($expcategories as $expcat)
                                        @if($expcat->id == $expense->expense_category_id)
                                        <option value="{{ $expcat->id }}" selected>{{ $expcat->name }}</option>
                                        @else
                                        <option value="{{ $expcat->id }}">{{ $expcat->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Is Cost Of Sale?</label>
                                <select name="is_cost_of_sale" class="form-select form-select-sm mb-1">
                                    @if($expense->is_cost_of_sale)
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                    @else
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                    @endif
                                </select>
                            </div> -->
                            @if ($settings->estimate_withholding_tax)
                                <div class="col-md-3">
                                    <label class="form-label">Is this Expense contains Withholding Tax</label>
                                    <select onchange="yesnoCheck(this)" class="form-control form-control-sm mb-1">
                                        <option value="no">NO</option>
                                        <option value="yes">YES</option>
                                    </select>
                                </div>
                                <div class="col-md-3" id="ifYes" style="display: none;">
                                    <label>Withholding Tax Rate(%): </label>
                                    <input type='number' min="0" id='wtax_rate' name='wht_rate'
                                        class="form-control form-control-sm mb-1"
                                        placeholder="Please Enter the Rate(%) of Withholding Tax">
                                </div>
                            @endif

                            <div class="col-md-3">
                                <label for="supplier_id" class="form-label">{{ trans('navmenu.supplier') }}</label>
                                <select name="supplier_id" id="supplier-m" required
                                    class="form-select form-select-sm mb-1" onchange="changeSupplier(this)">
                                    @foreach ($suppliers as $key => $supplier)
                                    @if($supplier->id == $expense->supplier_id)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @else
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ trans('navmenu.exp_type') }} <span style="color: red;">*</span></label>
                                <select name="exp_type" id="exp_type-m" onchange="wegExpTypeModal(this)" class="form-select form-select-sm mb-1" required>
                                    @if($expense->exp_type == 'cash')
                                    <option value="cash">{{ trans('navmenu.cash_exp') }}</option>
                                    <option value="credit">{{ trans('navmenu.credit_exp') }}</option>
                                    @else
                                    <option value="credit">{{ trans('navmenu.credit_exp') }}</option>
                                    <option value="cash">{{ trans('navmenu.cash_exp') }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class=" col-md-3">
                                <label for="account" class="form-label">{{ trans('navmenu.paid_from') }} <span style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-select form-select-sm mb-1" name="account" required>
                                    <option>{{$expense->account}}</option>
                                    <option value="Petty Cash">Petty Cash</option>
                                    <option value="Cash">{{ trans('navmenu.cash') }}</option>
                                    <option value="Bank">{{ trans('navmenu.bank') }}</option>
                                    <option value="Mobile Money">{{ trans('navmenu.mobilemoney') }}</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="total" class="form-label">{{ trans('navmenu.purchase_order_no') }}</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="order_no" placeholder="{{ trans('navmenu.hnt_order_no') }}" name="order_no" value="{{$expense->order_no}}" />
                            </div>
                            <div class="col-md-3" id="invoice_no-m">
                                <label for="total" class="form-label">{{ trans('navmenu.invoice_no') }}</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="inv_no" placeholder="{{ trans('navmenu.hnt_invoice_no') }}" name="invoice_no" value="{{$expense->invoice_no}}" />
                            </div>
                            @if ($settings->is_service_per_device)
                                <div class="col-md-3">
                                    <label class="form-label">{{ trans('navmenu.device_number') }}</label>
                                    <select name="device_id" class="form-control form-control-sm mb-1">
                                        <option value="">{{ trans('navmenu.select_device') }}</option>
                                        @if (!is_null($devices))
                                            @foreach ($devices as $device)
                                                <option value="{{ $device->id }}">{{ $device->device_number }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            @endif
                        </div>
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                            <a href="javascript:history.back()" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
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
    window.addEventListener('DOMContentLoaded', function() {
        var $min = document.querySelector('[name="expense_date"]');
        $min.DatePickerX.init({
            mondayFirst: true,
            format: 'yyyy-mm-dd',
            maxDate: new Date()
        });
    });
</script>