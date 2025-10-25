@extends('layouts.app')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-1">
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

    <div class="row g-1">
        <div class="col-lg-11 col-md-11 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-uppercase">{{$shop->name}}</h6>
                    <hr>
                    <ul class="list-group list-group-custom list-group-flush">
                        <li class="list-group-item">
                            <div class="row g-1">
                                <div class="col-sm-9">
                                    <h6>Delete this shop Data (Products, Services, Invoices, Purchases, STO's, Expenses etc)</h6>
                                    Once you delete a shop data, there is no going back. Please be certain.
                                </div>
                                <div class="col-sm-3" style="vertical-align: middle;">
                                     <h6 class="mb-1"><a href="#" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmModal" data-backdrop="static" data-keyboard="false" style=" color: red;"> Delete Shop Data</a></h6>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

     <!-- Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Shop Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-12 text-center">
                        @if(!is_null($shop->logo_location))
                        <figure>
                            <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="" style="width: 60px; height: 60px">
                        </figure>
                        @endif
                        <h5>{{$shop->name}}</h5>
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <div class="col-md-12">
                        <a onclick="showDetails()" class="btn btn-outline-danger btn-sm" id="btn-details">I want to Delete This Shop Data</a>
                        <div id="warning-pills-ack" style="display: none;">
                           <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="fa fa-warning"></i> Unexpected bad things will happen if you don’t read this!
                            </div> <hr>
                            <div class="mt-2 text-left">
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                  <i class="fa fa-times-circle"></i> This will permanently delete All data for <strong>{{$shop->name}}</strong> Including Products, Services, Purchases, Invoices, STO's, Expenses etc
                                </div>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                  <i class="fa fa-times-circle"></i>This will not change your billing plan and Business Settings. If you want to change, you can do so in your Settings.
                                </div>
                            </div>

                            <a onclick="showForm()" class="btn btn-outline-primary btn-sm" id="btn-next">I have read and understand these effects</a>
                        </div>
                    </div>
                    <form class="form row" method="POST" action="{{ url('clear-data') }}" id="delete-form" style="display: none;">
                        @csrf
                        <input type="hidden" name="shop_id" value="{{$shop->id}}">
                        <div class="col-md-12">
                            <label class="control-label">To confirm, type "<b>{{$shop->name}}</b>" in the box below <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="text" name="confirm_name" class="form-control form-control-sm mb-1" required>
                        </div>
                        <button type="submit" class="btn btn-outline-danger btn-sm" id="btn-submit">Delete This Shop Data</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-scripts')
<script>
    $(document).ready(function() {
        var btndet = document.getElementById('btn-details');
        var warnsect = document.getElementById('warning-pills-ack');
        var btnnext = document.getElementById('btn-next');
        var delform = document.getElementById('delete-form');

        $('#btn-details').on('click', function() {
            btndet.style.display = 'none';
            warnsect.style.display = 'block';
        });

        $('#btn-next').on('click', function() {
            warnsect.style.display = 'none';
            delform.style.display = 'block';
        });
    });
</script>
@endsection