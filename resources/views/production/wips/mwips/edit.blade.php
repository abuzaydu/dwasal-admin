@extends('layouts.prod')

<script>
  function confirmEditItem() {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_edit')}}",
          text: "{{trans('navmenu.will_affect')}}",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.yes_update')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('edititem').submit();
            Swal.fire(
              "{{trans('navmenu.updated')}}",
              "{{trans('navmenu.update_success')}}",
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
            <div class="col-lg-8 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item">{{$title}}</li>
                    <li class="breadcrumb-item active">{{$material->title}}</li>
                </ul>
            </div>            
            <div class="col-lg-4 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form id="edititem" class="row g-3 form-validate" method="POST" action="{{ route('prod-mwips.update', encrypt($mwip->id)) }}">
                        @csrf
                        {{ method_field('PATCH') }}
                        <div class="col-md-2">
                            <label class="form-label">Date</label>
                            <div class="inner-addon left-addon">
                                <i class="myaddon fa fa-calendar"></i>
                                <input type="text" name="date" id="date" value="{{$mwip->date}}" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Opening QTY</label>
                                <input type="number" step="any" name="opening_qty" class="form-control form-control-sm mb-1" value="{{$mwip->opening_qty}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Produced QTY</label>
                                <input type="number" step="any" name="produced" class="form-control form-control-sm mb-1" value="{{$mwip->produced}}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Moved to Finished</label>
                                <input type="number" step="any" name="used" class="form-control form-control-sm mb-1" value="{{$mwip->used}}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Loss/Wastage</label>
                                <input type="number" step="any" name="dam_qty" class="form-control form-control-sm mb-1" value="{{$mwip->dam_qty}}">
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-md-12">
                            <a href="#" class="btn btn-success btn-sm" onclick="confirmEditItem()" id="btn-submit">{{trans('navmenu.btn_save')}}
                            </a>
                             <a href="javascript:history.back()" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>                
    </div>
    <!--end row-->
@endsection

    <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
    <script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="date"]');
    
            $min.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>