@extends('layouts.gen')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>           
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    @if($shop->is_warehouse)
    <div class="row g-1">
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button expanded" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo"><i class='fa fa-sort-amount-desc'></i> -  {{ trans('navmenu.inventory_reports') }} </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse expanded" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <ul class="list-group list-group-custom list-group-flush">
                                        <li class="list-group-item"><a target="_blank" href="{{ url('stock-capital') }}"><i class="fa fa-check"></i> {{ trans('navmenu.current_stock_capital') }} </a></li>
                                        <li class="list-group-item"><a target="_blank" href="{{ url('stock-reports') }}"><i class="fa fa-check"></i> {{ trans('navmenu.stock_status_report') }} </a></li>
                                        <li class="list-group-item"><a target="_blank" href="{{ url('daily-closing-stock-report')}}"><i class="fa fa-check"></i> {{trans('navmenu.daily_closing_stock_report')}} </a></li>
                                        <li class="list-group-item"><a target="_blank" href="{{ url('stock-taking') }}"><i class="fa fa-check"></i> @if($shop->business_type_id == 1) Inventory Production Report @else Inventory Purchase Report @endif</a></li>
                                        <li class="list-group-item"><a target="_blank" href="{{ url('po-item-status-report') }}"><i class="fa fa-check"></i> PO Receiving Report</a></li>
                                        <li class="list-group-item"><a target="_blank" href="{{ url('stock-expires') }}"><i class="fa fa-check"></i> {{ trans('navmenu.expiration_report') }}</a></li>
                                        <li class="list-group-item"><a target="_blank" href="{{ url('reorder-reports') }}"><i class="fa fa-check"></i> {{ trans('navmenu.re_ordering_report') }}</a></li>
                                        <li class="list-group-item"><a target="_blank" href="{{ url('transfer-report') }}"><i class="fa fa-check"></i> {{ trans('navmenu.transfer_report') }} </a></li>
                                        <li class="list-group-item"><a target="_blank" href="{{ url('supplier-credit-reports')}}"><i class="fa fa-check"></i>  Supplier Credit Reports</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row g-1">
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-uppercase">Shop/Store Reports</h6>
                    <hr>
                    <div class="accordion" id="accordionExample">
                        @if($settings->is_cm_business)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne"> <i class="fa fa-pie-chart"></i> - Motorbike Business Reports </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <ul class="list-group list-group-custom list-group-flush">
                                        <li class="list-group-item"><a target="_blank" href="{{ url('daily-deposit-report') }}"><i class="fa fa-check"></i> {{ trans('navmenu.daily_deposit_report') }}</a></li>
                                        <li class="list-group-item"><a target="_blank" href="{{ url('monthly-deposit-report') }}"><i class="fa fa-check"></i> {{ trans('navmenu.monthly_deposit_report') }}</a></li>
                                        <!-- <li class="list-group-item"><a href="{{ url('monthly-collection-report') }}"><i class="fa fa-check"></i> {{ trans('navmenu.monthly_collection_report') }}</a></li> -->
                                        <li class="list-group-item"><a target="_blank" href="{{ url('tl-daily-performance-report') }}"><i class="fa fa-check"></i> {{ trans('navmenu.tl_daily_performance_report') }}</a></li>
                                        <li class="list-group-item"><a target="_blank" href="{{ url('tl-monthly-performance-report') }}"><i class="fa fa-check"></i> {{ trans('navmenu.tl_monthly_performance_report') }}</a></li>
                                        <li class="list-group-item"><a target="_blank" href="{{ url('monthly-profit-report') }}"><i class="fa fa-check"></i> {{ trans('navmenu.monthly_profit_report') }}</a></li>
                                        <li class="list-group-item"><a target="_blank" href="{{ url('contract-status-report') }}"><i class="fa fa-check"></i> {{ trans('navmenu.contract_status_report') }}</a></li>
                                        <li class="list-group-item"><a target="_blank" href="{{ url('working-riders-report') }}"><i class="fa fa-check"></i> {{ trans('navmenu.working_riders_report') }}</a></li>
                                        <li class="list-group-item"><a target="_blank" href="{{ url('upcoming-graduation-report') }}"><i class="fa fa-check"></i> {{ trans('navmenu.upcoming_graduation_report') }}</a></li>
                                        <li class="list-group-item"><a target="_blank" href="{{ url('contract-to-terminate') }}"><i class="fa fa-check"></i> {{ trans('navmenu.contract_to_terminate') }}</a></li>
                                        <li class="list-group-item"><a target="_blank" href="{{ url('over-deposited') }}"><i class="fa fa-check"></i> {{ trans('navmenu.over_deposited') }}</a></li>
                                        <li class="list-group-item"><a target="_blank" href="{{ url('monthly-reg-report') }}"><i class="fa fa-check"></i> {{ trans('navmenu.monthly_reg_report') }}</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne"> <i class="fa fa-area-chart"></i> - Shop Summary Reports </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <ul class="list-group list-group-custom list-group-flush">
                                        <li class="list-group-item"><a target="_blank" href="{{ url('dreport-summary') }}"><i class="fa fa-check"></i> {{ trans('navmenu.report_summary') }}</a></li>
                                        <li class="list-group-item"><a target="_blank" href="{{ url('detailed-daily-profit-loss') }}"><i class="fa fa-check"></i> {{ trans('navmenu.gr_report') }}</a></li>
                                        <li class="list-group-item"><a target="_blank" href="{{ url('total-report') }}"><i class="fa fa-check"></i> {{ trans('navmenu.daily_profit_loss_report') }}</a></li>

                                        @if($settings->enable_trip_logs)
                                        <li class="list-group-item"><a target="_blank" href="{{ url('trip-logs-report')}}"><i class="fa fa-check"></i>Vehicle Trip Logs Report</a></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="true" aria-controls="collapseOne"><i class='fa fa-bar-chart-o'></i> - {{ trans('navmenu.sales_summary_reports') }} </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <ul class="list-group list-group-custom list-group-flush">
                                        <li class="list-group-item"><a href="{{ url('sales-report') }}"><i class="fa fa-check"></i>  {{ trans('navmenu.sales_report') }}</a></li>
                                        <li class="list-group-item"><a href="{{ url('debts-report') }}"><i class="fa fa-check"></i>  {{ trans('navmenu.debt_report') }}</a></li>
                                        @if(Auth::user()->can('view-invoice-report'))
                                        <li> <a href="{{ url('invoice-report') }}">{{ trans('navmenu.invoice_report') }}</a></li>
                                        @endif

                                        @if($products > 1)

                                        <li class="list-group-item"><a href="{{ url('top-selling-products') }}"><i class="fa fa-list"></i> Top Selling Products</a></li>
                                        <li class="list-group-item"><a href="{{ url('sales-by-product') }}"><i class="fa fa-check"></i> {{ trans('navmenu.sales_by_product') }}</a></li>
                                        <li class="list-group-item"><a href="{{ url('profits') }}"><i class="fa fa-check"></i> {{ trans('navmenu.profit_report') }}</a></li>
                                        <li class="list-group-item"><a href="{{ url('sales-return-report') }}"><i class="fa fa-check"></i>  {{ trans('navmenu.sales_return_report') }}</a></li>
                                        @endif
                                        @if($services > 1)
                                        <li class="list-group-item"><a href="{{ url('sales-by-service') }}"><i class="fa fa-check"></i> {{ trans('navmenu.sales_by_service') }}</a></li>
                                        @if($settings->is_rental_service)
                                        <li class="list-group-item"><a href="{{ url('rental-status-report') }}"><i class="fa fa-check"></i>  Rental Status Report</a></li>
                                        @endif
                                        @endif
                                        <li class="list-group-item"><a href="{{url('discount-by-sales')}}"><i class="fa fa-check"></i>  Discount Reports</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree"><i class="fa fa-line-chart"></i> - {{ trans('navmenu.financial_report') }} </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <ul class="list-group list-group-custom list-group-flush">
                                        <li class="list-group-item"><a href="{{ url('daily-cash-flow-statement') }}"><i class="fa fa-check"></i> {{ trans('navmenu.daily_cashflow_stmt') }}</a></li>
                                        <li class="list-group-item"><a href="{{ url('petty-cash-report') }}"><i class="fa fa-check"></i> Petty Cash Report </a></li>
                                        <li class="list-group-item"><a href="{{ url('expenses-report') }}"><i class="fa fa-check"></i> {{ trans('navmenu.expense_report') }} </a></li>
                                        <li class="list-group-item"><a href="{{ url('account-statements') }}"><i class="fa fa-check"></i>  Account Statements</a></li>
                                        <li class="list-group-item"><a href="{{ url('cash-flow-statement') }}"><i class="fa fa-check"></i> {{ trans('navmenu.cash_flow_stmt') }}</a></li>
                                        <li class="list-group-item"><a href="{{ url('income-statement') }}"><i class="fa fa-check"></i> {{ trans('navmenu.income_stmt') }} </a></li>
                                        <li class="list-group-item"><a href="{{ url('business-value') }}"><i class="fa fa-check"></i> {{ trans('navmenu.business_value') }}</a></li>
                                        <li class="list-group-item"><a href="{{ url('closing-business-value') }}"><i class="fa fa-check"></i> {{ trans('navmenu.monthly_value') }}</a></li>
                                        <li class="list-group-item"><a href="{{ url('collections-report') }}"><i class="fa fa-check"></i> {{ trans('navmenu.collections_report') }}</a></li>

                                        @if($settings->is_agent)
                                        <li class="list-group-item"><a href="{{ url('open-closing-amount-statement') }}"><i class="fa fa-check"></i> {{ trans('navmenu.oca_stmt') }} </a></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @if ($shop->business_type_id != 3)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo"><i class='fa fa-sort-amount-desc'></i> -  {{ trans('navmenu.inventory_reports') }} </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <ul class="list-group list-group-custom list-group-flush">
                                        <li class="list-group-item"><a href="{{ url('stock-capital') }}"><i class="fa fa-check"></i> {{ trans('navmenu.current_stock_capital') }} </a></li>
                                        <li class="list-group-item"><a href="{{ url('stock-reports') }}"><i class="fa fa-check"></i> {{ trans('navmenu.stock_status_report') }} </a></li>
                                        <li class="list-group-item"><a href="{{ url('daily-closing-stock-report')}}"><i class="fa fa-check"></i> {{trans('navmenu.daily_closing_stock_report')}} </a></li>
                                        <li class="list-group-item"><a href="{{ url('stock-taking') }}"><i class="fa fa-check"></i> @if($shop->business_type_id == 1) Inventory Production Report @else Inventory Purchase Report @endif</a></li>
                                        <li class="list-group-item"><a href="{{ url('po-item-status-report') }}"><i class="fa fa-check"></i> PO Receiving Report</a></li>
                                        <li class="list-group-item"><a href="{{ url('stock-expires') }}"><i class="fa fa-check"></i> {{ trans('navmenu.expiration_report') }}</a></li>
                                        <li class="list-group-item"><a href="{{ url('reorder-reports') }}"><i class="fa fa-check"></i> {{ trans('navmenu.re_ordering_report') }}</a></li>
                                        <li class="list-group-item"><a href="{{ url('transfer-report') }}"><i class="fa fa-check"></i> {{ trans('navmenu.transfer_report') }} </a></li>
                                        <li class="list-group-item"><a href="{{ url('supplier-credit-reports')}}"><i class="fa fa-check"></i>  Supplier Credit Reports</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-uppercase">Company Reports</h6>
                    <hr>
                    <ul class="list-group list-group-custom list-group-flush">
                        <li class="list-group-item">
                            <div class="row g-1">
                                <div class="col-sm-2">
                                    <img class="invoice-logo" src="{{asset('side/assets/img/mr.png')}}" alt="" width="30">
                                </div>
                                <div class="col-sm-10">
                                    <h6 class="mb-1"><a target="_blank" href="{{ url('management-report') }}">Management Report</a></h6>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row g-1">
                                <div class="col-sm-2">
                                    <img class="invoice-logo" src="{{asset('side/assets/img/gl.png')}}" alt="" width="30">
                                </div>
                                <div class="col-sm-10">
                                    <h6 class="mb-1"><a target="_blank" href="{{ url('general-ledger') }}"> General Ledger</a></h6>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row g-1">
                                <div class="col-sm-2">
                                    <img class="invoice-logo" src="{{asset('side/assets/img/trial.jpg')}}" alt="" width="30">
                                </div>
                                <div class="col-sm-10">
                                    <h6 class="mb-1"><a target="_blank" href="{{ url('trial-balance') }}"> Trial alance</a></h6>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row g-1">
                                <div class="col-sm-2">
                                    <img class="invoice-logo" src="{{asset('side/assets/img/is.png')}}" alt="" width="30">
                                </div>
                                <div class="col-sm-10" style="vertical-align: middle;">
                                     <h6 class="mb-1"><a target="_blank" class="link" href="{{ url('company-income-stmt') }}"> Income Statement</a></h6>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row g-1">
                                <div class="col-sm-2">
                                    <img class="invoice-logo" src="{{asset('side/assets/img/cf.png')}}" alt="" width="30">
                                </div>
                                <div class="col-sm-10">
                                    <h6 class="mb-1"><a target="_blank" href="{{ url('company-cf-stmt') }}"> Cash Flow Statement</a></h6>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row g-1">
                                <div class="col-sm-2">
                                    <img class="invoice-logo" src="{{asset('side/assets/img/bs.png')}}" alt="" width="30">
                                </div>
                                <div class="col-sm-10" style="vertical-align: middle;">
                                     <h6 class="mb-1"><a target="_blank" href="{{ url('balance-sheet') }}"> Balance Sheet</a></h6>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row g-1">
                                <div class="col-sm-2">
                                    <img class="invoice-logo" src="{{asset('side/assets/img/bs.png')}}" alt="" width="30">
                                </div>
                                <div class="col-sm-10" style="vertical-align: middle;">
                                     <h6 class="mb-1"><a target="_blank" href="{{ url('monthly-balance-sheet') }}"> Monthly Balance Sheet</a></h6>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row g-1">
                                <div class="col-sm-2">
                                    <img class="invoice-logo" src="{{asset('side/assets/img/ms.png')}}" alt="" width="30">
                                </div>
                                <div class="col-sm-10">
                                    <h6 class="mb-1"><a target="_blank" href="{{ url('monthly-sales-report') }}">Monthly Sales Report</a></h6>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row g-1">
                                <div class="col-sm-2">
                                    <img class="invoice-logo" src="{{asset('side/assets/img/pl.png')}}" alt="" width="30">
                                </div>
                                <div class="col-sm-10">
                                    <h6 class="mb-1"><a target="_blank" href="{{url('consolidated')}}">{{trans('navmenu.consolidated_report')}}</a></h6>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection