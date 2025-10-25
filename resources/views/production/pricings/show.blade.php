@extends('layouts.prod')
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

    <div class="row g-3">
        <div class="col-12">
            <div class="card pt-2">
                <div class="card-body">
                    <div class="row g-3 print_invoice" id="print-pc">
                        <div class="col-md-12">
                            <table class="table mb-1">
                                <tbody>
                                    <tr>
                                        <td colspan="2" style="text-align: center; background:  #2874a6;">
                                            <h6 class="mb-0 text-uppercase" style="color: #fff;">{{$title}}</h6>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <table class="item mb-0" style="width: 100%;">
                                <tbody>
                                    <tr>
                                        <td>Product: </td>
                                        <th><b>{{$product->name}}</b></th>
                                    
                                        <td>{{trans('navmenu.date')}} :</td>
                                        <th><b>{{date("d, M Y", strtotime($pricing->date))}}</b></th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Material Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 40%; text-transform: uppercase;">Item Description</th>
                                                        <th style="text-align: center; width: 20%;">Unit Price</th>
                                                        <th style="text-align: center; width: 20%;">No. Pieces Made</th>
                                                        <th style="text-align: right; width: 20%;">Cost Per Piece</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $tmcost = 0; ?>
                                                    @foreach($materialcosts as $mcost)
                                                    <?php $tmcost += $mcost->cost_per_piece; ?>
                                                    <tr class="item-row">
                                                        <td class="description">{{ $mcost->item_desc }}</td>
                                                        <td style="text-align: center;">
                                                            {{ number_format($mcost->unit_cost) }}
                                                        </td>
                                                        <td style="text-align: center;">
                                                            {{ $mcost->no_of_piece_made }}
                                                        </td>
                                                        <td style="text-align: right;">{{ number_format($mcost->cost_per_piece, 2, '.', ',') }}</td>
                                                    </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td colspan="3"><b>Total Material Cost</b></td>
                                                        <td style="text-align: right;"><b>{{ number_format($tmcost, 2,'.', ',') }}</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Labour Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 40%; text-transform: uppercase;">Production Stage</th>
                                                        <th style="text-align: center; width: 20%;">Daily Wage Rate</th>
                                                        <th style="text-align: center; width: 20%;">No. Pieces Made</th>
                                                        <th style="text-align: right; width: 20%;">Cost Per Piece</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $tlcost = 0; ?>
                                                    @foreach($labourcosts as $lcost)
                                                    <?php $tlcost += $lcost->cost_per_piece; ?>
                                                    <tr class="item-row">
                                                        <td class="description">{{ $lcost->stage }}</td>
                                                        <td style="text-align: center;">
                                                            {{ $lcost->daily_wage_rate }}
                                                        </td>
                                                        <td style="text-align: center;">
                                                            {{ $lcost->no_of_piece }}
                                                        </td>
                                                        <td style="text-align: right;">{{ number_format($lcost->cost_per_piece, 2, '.', ',') }}</td>
                                                    </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td colspan="3"><b>Total Labour Cost</b></td>
                                                        <td style="text-align: right;"><b>{{ number_format($tlcost, 2, '.', ',') }}</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Transport Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 40%; text-transform: uppercase;">Description</th>
                                                        <th style="text-align: center; width: 20%;">Transport Cost</th>
                                                        <th style="text-align: center; width: 20%;">No. Items</th>
                                                        <th style="text-align: right; width: 20%;">Cost Per Item</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $ttpcost = 0; ?>
                                                    @foreach($transportcosts as $tpcost)
                                                    <?php $ttpcost += $tpcost->cost_per_unit; ?>
                                                    <tr class="item-row">
                                                        <td class="description">{{ $tpcost->description }}</td>
                                                        <td style="text-align: center;">{{ $tpcost->transport_cost }}</td>
                                                        <td style="text-align: center;">{{ $tpcost->no_of_items }}</td>
                                                        <td style="text-align: right;">
                                                            {{ number_format($tpcost->cost_per_unit,2,'.',',') }}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td colspan="3"><b>Total Transport Cost</b></td>
                                                        <td style="text-align: right;"><b>{{ number_format($ttpcost,2,'.',',') }}</b></td>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Summary of Direct Costs</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <tbody>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td style="text-align: right;"><b>Total Material Costs</b></td>
                                                        <td style="text-align: right;"><b>{{ number_format($tmcost, 2, '.', ',') }}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td style="text-align: right;"><b>Total Labour Costs</b></td>
                                                        <td style="text-align: right;"><b>{{ number_format($tlcost, 2,'.', ',') }}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td style="text-align: right;"><b>Total Transport Cost</b></td>
                                                        <td style="text-align: right;"><b>{{ number_format($ttpcost, 2, '.', ',') }}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="3" style="text-align: right;"><b>SubTotal 1 </b></th>
                                                        <th style="text-align: right;"><b>{{ number_format(($tmcost+$tlcost+$ttpcost), 2, '.', ',') }}</b></th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <table class="table table-strpped mt-3">
                                <thead>
                                    <tr>
                                        <th>Local Indirect costs (Overhead costs)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 65%; text-transform: uppercase;">Description</th>
                                                        <th style="text-align: center; width: 15%;">Percentage(%)</th>
                                                        <th style="text-align: right; width: 20%;">Cost Per Item</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $tlpercent = 0; $tlocalicost = 0; ?>
                                                    @foreach($localindirectcosts as $icost)
                                                    <?php $tlpercent += $icost->percent; $tlocalicost += $icost->amount; ?>
                                                    <tr class="item-row">
                                                        <td class="description">
                                                            {{ $icost->description }}
                                                        </td>
                                                        <td style="text-align: center;">{{ $icost->percent+0 }}</td>
                                                        <td style="text-align: right;">
                                                            {{ number_format($icost->amount,2,'.',',') }}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td>Total Mark-up %</td>
                                                        <td style="text-align: center;">{{ $tlpercent }}</td>
                                                        <td style="text-align: right;">{{ number_format($tlocalicost,2,'.',',') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="2" style="font-size: 16px;"><b>TOTAL / EX Works Price</b></th>
                                                        <th style="text-align: right; font-size: 16px;"><b>{{ number_format(($tmcost+$tlcost+$ttpcost+$tlocalicost), 2, '.', ',') }}</b></th>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php $total_local_ex_wprice = ($tmcost+$tlcost+$ttpcost+$tlocalicost); ?>
                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Local Packaging Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 50%; text-transform: uppercase;">Item Description</th>
                                                        <th style="text-align: center; width: 15%;">Package Cost</th>
                                                        <th style="text-align: center; width: 15%;">No. Items</th>
                                                        <th style="text-align: right; width: 20%;">Cost Per Item</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $tlocalpackcost = 0; ?>
                                                    @foreach($localpackagecosts as $pcost)
                                                    <?php $tlocalpackcost += $pcost->unit_cost; ?>
                                                    <tr class="item-row">
                                                        <td class="description">
                                                            {{ $pcost->item_desc }}
                                                        </td>
                                                        <td style="text-align: center;">{{ $pcost->package_cost }}</td>
                                                        <td style="text-align: center;">{{ $pcost->no_of_items }}
                                                        </td>
                                                        <td style="text-align: right;">
                                                            {{ number_format($pcost->unit_cost, 2, '.',',') }}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td colspan="3"><b>Total Packaging Costs</b></td>
                                                        <td style="text-align: right;"><b>{{ number_format($tlocalpackcost, 2, '.',',') }}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="3" style="text-align: right; font-size: 16px;"><b>Local Sub Total : Ex-Works + Packaging </b></th>
                                                        <th style="text-align: right; font-size: 16px;"><b>{{ number_format(($total_local_ex_wprice+$tlocalpackcost), 2, '.', ',') }}</b></th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Export Indirect costs (Overhead costs)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 65%; text-transform: uppercase;">Description</th>
                                                        <th style="text-align: center; width: 15%;">Percentage(%)</th>
                                                        <th style="text-align: right; width: 20%;">Cost Per Item</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $tpercent = 0; $ticost = 0; ?>
                                                    @foreach($indirectcosts as $icost)
                                                    <?php $tpercent += $icost->percent; $ticost += $icost->amount; ?>
                                                    <tr class="item-row">
                                                        <td class="description">
                                                            {{ $icost->description }}
                                                        </td>
                                                        <td style="text-align: center;">{{ $icost->percent+0 }}</td>
                                                        <td style="text-align: right;">
                                                            {{ number_format($icost->amount,2,'.',',') }}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td>Total Mark-up %</td>
                                                        <td style="text-align: center;">{{ $tpercent }}</td>
                                                        <td style="text-align: right;">{{ number_format($ticost,2,'.',',') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="2" style="font-size: 16px;"><b>TOTAL / EX Works Price</b></th>
                                                        <th style="text-align: right; font-size: 16px;"><b>{{ number_format(($tmcost+$tlcost+$ttpcost+$ticost), 2, '.', ',') }}</b></th>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php $total_ex_wprice = ($tmcost+$tlcost+$ttpcost+$ticost); ?>
                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Export Packaging Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 50%; text-transform: uppercase;">Item Description</th>
                                                        <th style="text-align: center; width: 15%;">Package Cost</th>
                                                        <th style="text-align: center; width: 15%;">No. Items</th>
                                                        <th style="text-align: right; width: 20%;">Cost Per Item</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $tpackcost = 0; ?>
                                                    @foreach($packagecosts as $pcost)
                                                    <?php $tpackcost += $pcost->unit_cost; ?>
                                                    <tr class="item-row">
                                                        <td class="description">
                                                            {{ $pcost->item_desc }}
                                                        </td>
                                                        <td style="text-align: center;">{{ $pcost->package_cost }}</td>
                                                        <td style="text-align: center;">{{ $pcost->no_of_items }}
                                                        </td>
                                                        <td style="text-align: right;">
                                                            {{ number_format($pcost->unit_cost, 2, '.',',') }}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td colspan="3"><b>Total Packaging Costs</b></td>
                                                        <td style="text-align: right;"><b>{{ number_format($tpackcost, 2, '.',',') }}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="3" style="text-align: right; font-size: 16px;"><b>Export Sub Total: Ex-Works + Packaging </b></th>
                                                        <th style="text-align: right; font-size: 16px;"><b>{{ number_format(($total_ex_wprice+$tpackcost), 2, '.', ',') }}</b></th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php $totalcost = ($total_ex_wprice+$tpackcost); ?>
                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Conversion to EUR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <thead>
                                                    <tr>
                                                        <th>Description</th>
                                                        <th style="text-align: center;">Currency</th>
                                                        <th style="text-align: center;">Ex. Rate</th>
                                                        <th style="text-align: right;">Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td style="font-size: 14px;"><b>Ex-Works + Packaging </b></td>
                                                        <td style="text-align: center;">{{ $pricing->currency }}</td>
                                                        <td style="text-align: center;">{{ number_format($pricing->ex_rate) }}
                                                        </td>
                                                        <td style="text-align: right; font-size: 14px;"><b>{{ number_format($totalcost/$pricing->ex_rate, 2,'.',',') }}</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Export handling costs</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 75%; text-transform: uppercase;">Description</th>
                                                        <th style="text-align: right; width: 25%;">Cost Per Item</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $thcost = 0; ?>
                                                    @foreach($handlingcosts as $hcost)
                                                    <?php $thcost += $hcost->amount; ?>
                                                    <tr class="item-row">
                                                        <td class="description">
                                                            {{ $hcost->description }}
                                                        </td>
                                                        <td style="text-align: right;">{{ number_format($hcost->amount, 2,'.',',') }}</td>
                                                    </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td><b>Total Export handling Cost</b></td>
                                                        <td style="text-align: right;"><b>{{ number_format($thcost, 2,'.',',') }}</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Calculating FOB percentage margin:</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <tbody>
                                                    <tr>
                                                        <td><b>Min. order value / MOQ (enter your minimum order value here)</b></td>
                                                        <td style="text-align: right;">{{ $pricing->min_order_value }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>FOB percentage margin to factor into costing and pricing</b></td>
                                                        <td style="text-align: right;"><b>{{ number_format(($thcost/$pricing->min_order_value)*100, 2,'.', ',') }}</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-strpped mt-4">
                                <thead>
                                    <tr>
                                        <th>Final FOB price:</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="2"><b>Sub Total 2: Ex-Works + Packaging</b></td>
                                                        <td style="text-align: right; font-size: 14px;"><b>{{ number_format($totalcost/$pricing->ex_rate, 2,'.', ',') }}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>FOB percentage margin</b></td>
                                                        <td style="text-align: center;"><b>{{ number_format(($thcost/$pricing->min_order_value)*100, 2,'.', ',') }}</b></td>
                                                        <td style="text-align: right;"><b>{{ number_format(($totalcost/$pricing->ex_rate)*($thcost/$pricing->min_order_value), 2,'.', ',') }}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2"><b>FINAL FOB PRICE</b></td>
                                                        <td style="text-align: right;"><b>{{ number_format(($totalcost/$pricing->ex_rate)+(($totalcost/$pricing->ex_rate)*($thcost/$pricing->min_order_value)), 2,'.', ',') }}</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>SET pRICING:</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <tbody>
                                                    <tr>
                                                        <td><b>Number of pieces per set</b></td>
                                                        <td style="text-align: center;">
                                                            {{ $pricing->no_of_piece_per_set }}
                                                        </td>
                                                        <td colspan="2"><b>Set FOB price</b></td>
                                                        <td style="text-align: right; font-size: 14px;"><b>{{ number_format(($totalcost/$pricing->ex_rate)+(($totalcost/$pricing->ex_rate)*($thcost/$pricing->min_order_value))*$pricing->no_of_piece_per_set, 2,'.', ',') }}</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-3 mb-1">
            <a href="{{ url('set-product-price/'.encrypt($pricing->id)) }}" class="btn btn-success btn-sm float-end" style="margin: 5px;"><i class="fa fa-check"></i> Set Prices to Product</a>
            <a href="#" class="btn btn-primary btn-sm twoToneButton float-end" style="margin: 5px;"><i class="fa fa-download"></i> Download PDF /Print <span class="load loading"></span></a>
        </div>
    </div> <!-- .row end -->
@endsection

@section('page-scripts')
    <script>
        $(document).ready(function() {
            $('#product_id').on('change', function(){
                var selection = $('#product_id').val();
                var product_id = selection.replace('number:', '');
                angular.element(document.getElementById('mycontroller')).scope().updatePricingProduct(product_id);
            });

            $('#date').on('change', function(){
                var selection = $('#date').val();
                angular.element(document.getElementById('mycontroller')).scope().updatePricingDate(selection);
            });
        });
    </script>
@endsection

<link rel="stylesheet" type="text/css" href="{{ asset('css/receipt.css') }}">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js" integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous"></script>
    <script>    
        $(document).ready( function ()  {
            $('#btnPrint').on("click", function(e) {

                if ($("#printer").length) {
                    $("#printer").remove();
                }

                var divElements = $("#receipt").html();
                var iframe = $('<iframe class="hidden" id="printer"></iframe>').appendTo('body');
                var printer = $('#printer');
                printer.contents().find('body').append('<!DOCTYPE html><head><title>Print Title</title><link href="https://fonts.cdnfonts.com/css/lt-binary-neue" rel="stylesheet"></head><body>' + divElements + '</body>');
                setTimeout(function() {  
                    document.title = "<?php echo $product->name.'_'.$pricing->created_at; ?>";
                    printer.get(0).contentWindow.print();

                }, 250);
            });
        });
    </script>

    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript">
        $(function(){
            var twoToneButton = document.querySelector('.twoToneButton');
            twoToneButton.addEventListener("click", function() {
                twoToneButton.innerHTML = "Preparing PDF";
                twoToneButton.classList.add('spinning');
                savePdf(twoToneButton);
            }, false);
            
        });

        function savePdf(twoToneButton) {
            const element = document.getElementById("print-pc");
            var filename = "<?php echo $product->name.'_'.$pricing->created_at; ?>";
            var opt = {
                margin: 0.5,
                filename:     filename+'.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                // Added after option to add spacing after page break
                pagebreak: { avoid: "tr, img", mode: "css", before: 'always' },
                html2canvas: { scale: 2, useCORS: true, dpi: 192, letterRendering: true, scrollY: 0, scrollX: 0 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait', putTotalPages: true }
            };

            // New Promise-based usage:
            html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                twoToneButton.classList.remove('spinning');
                twoToneButton.innerHTML = "Print Preview";
                window.open(pdf.output('bloburl'), '_blank');
            });
        }
    </script>