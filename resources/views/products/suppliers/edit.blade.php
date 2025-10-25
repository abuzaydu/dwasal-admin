@extends('layouts.inv')
<script>

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
             document.getElementById('delete-form-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
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
            <div class="col-lg-5 col-md-5 col-sm-3">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Suppliers</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-3 text-right">
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix">
        <div class="col-md-12 mx-auto " >
            <h6 class="mb-0 text-uppercase text-center">{{trans('navmenu.add_new_supplier')}}</h6>
            <hr>
            <div class="card radius-6">
                <div class="card-body">
                    <form class="form row g-1" method="POST" action="{{ route('suppliers.update', encrypt($supplier->id)) }}">
                        @csrf
                        {{ method_field('PATCH') }} 
                        <input type="hidden" name="supplier_for" value="Stock">
                        <div class="col-md-3" >
                            <label for="name" class="form-label">{{trans('navmenu.supplier_name')}}</label>
                            <input type="text" name="name" class="form-control form-control-sm mb-1" value="{{$supplier->name}}" required placeholder="{{trans('navmenu.hnt_supplier_name')}}"> 
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.contact_number')}}</label>
                            <input id="phone" type="tel" name="contact_no" placeholder="{{trans('navmenu.hnt_contact_number')}}  Eg. 0789XXXXXX" class="form-control form-control-sm mb-1" value="{{$supplier->contact_no}}" data-inputmask='"mask": "9999999999"' data-mask>
                        </div> 
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.email_address')}}</label>
                            <input type="text" name="email" placeholder="{{trans('navmenu.hnt_supplier_email')}}" class="form-control form-control-sm mb-1" value="{{$supplier->email}}">
                        </div>  
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.address')}}</label>
                            <input type="text" name="address" placeholder="{{trans('navmenu.address')}}" class="form-control form-control-sm mb-1" value="{{$supplier->address}}">
                        </div>
                        <div class="col-md-3">
                            <label for="account" class="form-label">Account Number</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="account" name="account_number" value="{{$supplier->account_number}}" placeholder="Account Number">
                        </div>
                        <div class="col-md-3">
                            <label for="account" class="form-label">Account Name</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="account" name="account_name" value="{{$supplier->account_name}}" placeholder="Account Name">
                        </div>
                        <div class="col-md-3">
                            <label for="swift_code" class="form-label">Swift Code</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="swift_code" name="swift_code" value="{{$supplier->swift_code}}" placeholder="Swift Code">
                        </div>
                        <div class="col-md-3">
                            <label for="bank_name" class="form-label">Bank Name</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="bank_name" name="bank_name" value="{{$supplier->bank_name}}" placeholder="Bank Name">
                        </div>
                        <div class="col-md-3">
                            <label for="bank_name" class="form-label">Branch Name</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="branch_name" name="branch_name" value="{{$supplier->branch_name}}" placeholder="Branch Name">
                        </div>
                        <div class="col-md-3 pt-4">
                            <button type="submit" class="btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                            <button type="reset" class="btn btn-warning btn-sm">{{trans('navmenu.btn_reset')}}</button>
                        </div>
                    </form>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>     
    </div>
@endsection


