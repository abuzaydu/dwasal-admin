@extends('layouts.prof')
<style type="text/css">
    #fullpage {
      display: none;
      position: absolute;
      z-index: 9999;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background-size: contain;
      background-repeat: no-repeat no-repeat;
      background-position: center center;
      background-color: black;
    }
</style>
<script type="text/javascript">
    function getPics() {} //just for this demo
    const imgs = document.querySelectorAll('.gallery img');
    const fullPage = document.querySelector('#fullpage');

    imgs.forEach(img => {
      img.addEventListener('click', function() {
        fullPage.style.backgroundImage = 'url(' + img.src + ')';
        fullPage.style.display = 'block';
      });
    });

    function changeColor(color) {
        document.getElementById('invoice-color').value = color;
    }
</script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-1">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                         
                    <li class="breadcrumb-item">Settings</li>    
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row" onload="getPics()">
        <div class="col-md-5 text-center">
            <h6>Selected Template</h6>
            <hr>
            @foreach ($templates as $pkey => $temp)
            @if($settings->invoice_temp == $pkey)
            <figure class="gallery">
                <a href="{{ url('/img/'.$temp) }}"><img src="{{asset('/img/'.$temp)}}" alt="{{asset('/img/'.$temp)}}" width="200" style="border: 1px solid white;"></a>

            </figure>
            {{$pkey}}
            @endif
            @endforeach
        </div>
        <div class="col-md-7">
            <h6>Select New Template</h6>
            <hr>
            <div class="card">
                <div class="card radius-6 p-4" id="print-permission-page">
                    <form class="row g-3" action="{{ route('settings.update', encrypt($settings->id)) }}" method="POST">
                        <!-- Horizontal Form -->
                        @csrf
                        {{ method_field('PATCH') }}
                        <input type="hidden" name="is_inv_temp_update" value="1">
                        @foreach ($templates as $pkey => $temp)
                        <div class="col-md-6 text-center ">
                            <label style="padding-bottom: 5px; page-break-inside:avoid; page-break-after:auto; font-weight: normal;">
                                @if($settings->invoice_temp == $pkey)
                                <input type="checkbox" checked name="invoice_temp" value="{{$pkey}}"> 
                                @else
                                <input type="checkbox" name="invoice_temp" value="{{$pkey}}"> 
                                @endif
                                <img class="invoice-logo" src="{{asset('/img/'.$temp)}}" alt="" width="100" style="border: 1px solid white;"> {{$pkey}}
                            </label><br>
                            <a href="{{ url('/img/'.$temp) }}"><i class="fa fa-eye"></i> Preview</a>.
                        </div>
                        @endforeach
                        <div class="col-md-12"></div>
                        <div class="col-sm-6">
                            <label class="form-label">Invoice Title </label>
                            <select name="invoice_title" class="form-select form-select-sm mb-1">
                                @if($settings->invoice_title == 'TAX INVOICE')
                                <option>{{$settings->invoice_title}}</option>
                                <option>INVOICE</option>
                                @else
                                <option>{{$settings->invoice_title}}</option>
                                <option>TAX INVOICE</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Invoice Title Color </label>
                            <select name="invoice_title_color" class="form-select form-select-sm mb-1">
                                @if($settings->invoice_title_color == 'white')
                                <option value="white">White</option>
                                <option value="black">Black</option>
                                @else
                                <option value="black">Black</option>
                                <option value="white">White</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-12">
                            <h6>Choose Invoice Titles Background Color</h6>
                            <input type="hidden" id="invoice-color" name="invoice_color" value="{{$settings->invoice_color}}">
                            <ul class="choose-skin list-unstyled row g-1">
                                @foreach($colors as $color)
                                @if($settings->invoice_color == $color)
                                <li class="col-sm-3 mb-2 active">
                                    <div class="purple" style="background-color: <?php echo $color; ?>"></div><span>{{$color}}</span>
                                </li>
                                @else
                                <li class="col-sm-3 mb-2" onclick="changeColor('<?php echo $color; ?>')">
                                    <div class="blue" style="background-color: <?php echo $color; ?>"></div><span>{{$color}}</span>
                                </li>
                                @endif
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Invoice End Note</label>
                            <input type="text" name="invoice_end_note" value="{{$settings->invoice_end_note}}" class="form-control form-control-sm mb-1" placeholder="Enter your End note">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success btn-sm">Update </button>
                            <a href="{{ url('settings')}}" class="btn btn-warning btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="fullpage" onclick="this.style.display='none';"></div>
    </div>
@endsection