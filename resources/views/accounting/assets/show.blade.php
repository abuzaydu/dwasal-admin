@extends('layouts.acc')

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
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">

            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <div class="row g-1 print_invoice" id="print-st">
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
                                            <td>Asset Name: </td>
                                            <th><b>{{$asset->asset_name}}</b></th>
                                            <td>Asset Class</td>
                                            <th><b>{{$asset->asset_class}}</b></th>
                                            <td>Desscription :</td>
                                            <th><b>{{$asset->description}}</b></th>
                                        </tr>
                                        <tr>
                                            <td>Physical Location:</td>
                                            <th><b>{{$asset->physical_location}}</b></th>
                                            <td>Asset Number :</td>
                                            <th><b>{{ $asset->asset_number }}</b></th>
                                            <td>Serial No:</td>
                                            <th><b>{{$asset->serial_no}}</b></th>
                                        </tr>
                                        <tr>
                                            <td>Acquistion Date :</td>
                                            <th><b>{{ $asset->acquisition_date }}</b></th>
                                            <td>Acquisition Cost ({{$currency}}):</td>
                                            <th><b>{{number_format($asset->acquisition_cost)}}</b></th>
                                            <td>Depreciation Method :</td>
                                            <th><b>{{ $asset->dep_method }}</b></th>
                                        </tr>
                                        <tr>
                                            <td>Useful Life:</td>
                                            <th><b>{{$asset->useful_life}}</b></th>
                                            <td>First Year (%) :</td>
                                            <th><b>{{ $asset->first_year }}</b></th>
                                            <td>Salvage Value:</td>
                                            <th><b>{{$asset->salvage_value}}</b></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12 pt-3">
                                <p class="mb-1 text-uppercase text-center">Depreciations</p>
                                <table class="items mt-0" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Year</th>
                                            <th style="text-align: right;">Value Begin of Year ({{$currency}}) </th>
                                            <th style="text-align: right;">Depreciation ({{$currency}})</th>
                                            <th style="text-align: right;">Value End of Year ({{$currency}})</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total = 0; ?>
                                        @foreach($depreciations as $key => $item)
                                        <?php $total += 0; ?>
                                        <tr>
                                            <td>{{$item->year}}</td>
                                            <td style="text-align: right;">{{number_format($item->value_begin_yr, 2, '.', ',')}}</td>
                                            <td style="text-align: right;">{{ number_format($item->dep_amount, 2, '.', ',') }}</td>
                                            <td style="text-align: right;">{{ number_format($item->value_begin_yr, 2, '.', ',') }}</td>
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
    </div>
    <!--end row-->
@endsection

<link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">

<script src="{{asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="acquisition_date"]');
            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>