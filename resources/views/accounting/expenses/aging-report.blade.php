@extends('layouts.acc')

@section('content')
      <div class="row">
        <div class="card radius-6">
          <div class="card-header align-items-center">

            <h5 class="float-start">{{trans('navmenu.aging_report')}}</h5>
            <div class="float-end"><a class="btn btn-success" href="{{url('expenses')}}">{{trans('navmenu.expenses')}} </a></div>
          </div>

          <div class="card-body" >
            <div class="row">
              <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                @if(!is_null($shop->logo_location))
                  <figure>
                    <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                  </figure>
                @endif
                  <h5>{{Session::get('shop_name')}}</h5>
                  <h6 class="title">{{trans('navmenu.aging_report')}} <br><b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h6>
              </div>
            </div>
                  <!-- Table row -->
            <div class="row">
              <div class="col-xs-12 table-responsive">
                <table id="aging-report" class="table table-striped display nowrap" style="width: 100%;">
                  <thead>
                  <tr>
                    <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.supplier_id')}}</th>
                    <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.supplier_name')}}</th>
                    <th style="text-align: center; text-transform: uppercase;">0-30({{trans('navmenu.days')}})</th>
                    <th style="text-align: center; text-transform: uppercase;">31-60({{trans('navmenu.days')}})</th>
                    <th style="text-align: center; text-transform: uppercase;">61-90({{trans('navmenu.days')}})</th>
                    <th style="text-align: center; text-transform: uppercase;">91-120({{trans('navmenu.days')}})</th>
                    <th style="text-align: center; text-transform: uppercase;">121-150({{trans('navmenu.days')}})</th>
                    <th style="text-align: center; text-transform: uppercase;">151-180({{trans('navmenu.days')}})</th>
                    <th style="text-align: center; text-transform: uppercase;">181-210({{trans('navmenu.days')}})</th>
                    <th style="text-align: center; text-transform: uppercase;">211-240({{trans('navmenu.days')}})</th>
                    <th style="text-align: center; text-transform: uppercase;">241-270({{trans('navmenu.days')}})</th>
                    <th style="text-align: center; text-transform: uppercase;">271-300({{trans('navmenu.days')}})</th>
                    <th style="text-align: center; text-transform: uppercase;">301-330({{trans('navmenu.days')}})</th>
                    <th style="text-align: center; text-transform: uppercase;">331-360({{trans('navmenu.days')}})</th>
                    <th style="text-align: center; text-transform: uppercase;">Over 360({{trans('navmenu.days')}})</th>
                    <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.total')}}</th>
                  </tr>
                  </thead>
                  <tbody>
                    <?php $d3total =0; $d6total=0; $d9total=0; $d12total=0; $d15total=0; $d18total=0; $d21total=0; $d24total=0; $d27total=0; $d30total=0; $d33total=0; $d36total=0; $dab36total = 0; $total = 0; ?>
                      @foreach($agings as $key => $aging)
                        <?php 
                          $d3total += $aging['0-30'];
                          $d6total += $aging['31-60'];
                          $d9total += $aging['61-90'];
                          $d12total += $aging['91-120'];
                          $d15total += $aging['121-150'];
                          $d18total += $aging['151-180'];
                          $d21total += $aging['181-210'];
                          $d24total += $aging['211-240'];
                          $d27total += $aging['241-270'];
                          $d30total += $aging['271-300'];
                          $d33total += $aging['301-330'];
                          $d36total += $aging['331-360'];
                          $dab36total += $aging['>360'];
                          $total += $aging['ctotal'];
                         ?>
                        <tr>
                          <td style="text-align: center;">{{sprintf('%03d', $aging['supp_id'])}}</td>
                          <td style="text-align: center;">{{$aging['name']}}</td>
                          <td style="text-align: center;">{{number_format($aging['0-30'], 0, '.', ',')}}</td>
                          <td style="text-align: center;">{{number_format($aging['31-60'], 0, '.', ',')}}</td>
                          <td style="text-align: center;">{{number_format($aging['61-90'], 0, '.', ',')}}</td>
                          <td style="text-align: center;">{{number_format($aging['91-120'], 0, '.', ',')}}</td>
                          <td style="text-align: center;">{{number_format($aging['121-150'], 0, '.', ',')}}</td>
                          <td style="text-align: center;">{{number_format($aging['151-180'], 0, '.', ',')}}</td>
                          <td style="text-align: center;">{{number_format($aging['181-210'], 0, '.', ',')}}</td>
                          <td style="text-align: center;">{{number_format($aging['211-240'], 0, '.', ',')}}</td>
                          <td style="text-align: center;">{{number_format($aging['241-270'], 0, '.', ',')}}</td>
                          <td style="text-align: center;">{{number_format($aging['271-300'], 0, '.', ',')}}</td>
                          <td style="text-align: center;">{{number_format($aging['301-330'], 0, '.', ',')}}</td>
                          <td style="text-align: center;">{{number_format($aging['331-360'], 0, '.', ',')}}</td>
                          <td style="text-align: center;">{{number_format($aging['>360'], 0, '.', ',')}}</td>
                          <td style="text-align: center;">{{number_format($aging['ctotal'], 0, '.', ',')}}</td>
                        </tr>
                        @endforeach
                  </tbody>
                  <tfoot>
                    <tr>
                      <th></th>
                      <th style="text-align: right; text-transform: uppercase;">{{trans('navmenu.total')}}</th>
                      <th style="text-align: center;">{{number_format($d3total, 0, '.', ',')}}</th>
                      <th style="text-align: center;">{{number_format($d6total, 0, '.', ',')}}</th>
                      <th style="text-align: center;">{{number_format($d9total, 0, '.', ',')}}</th>
                      <th style="text-align: center;">{{number_format($d12total, 0, '.', ',')}}</th>
                      <th style="text-align: center;">{{number_format($d15total, 0, '.', ',')}}</th>
                      <th style="text-align: center;">{{number_format($d18total, 0, '.', ',')}}</th>
                      <th style="text-align: center;">{{number_format($d21total, 0, '.', ',')}}</th>
                      <th style="text-align: center;">{{number_format($d24total, 0, '.', ',')}}</th>
                      <th style="text-align: center;">{{number_format($d27total, 0, '.', ',')}}</th>
                      <th style="text-align: center;">{{number_format($d30total, 0, '.', ',')}}</th>
                      <th style="text-align: center;">{{number_format($d33total, 0, '.', ',')}}</th>
                      <th style="text-align: center;">{{number_format($d36total, 0, '.', ',')}}</th>
                      <th style="text-align: center;">{{number_format($dab36total, 0, '.', ',')}}</th>
                      <th style="text-align: center;">{{number_format($total, 0, '.', ',')}}</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          <div class="row" style="border-top: 2px solid #82B1FF;">
              <div class="col-xs-6">
                <span>{{Session::get('shop_name')}} Invoices</span>
              </div>
              <div class="col-xs-6">
                <span class="float-end">Generated on: {{$reporttime}}</span>
              </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
