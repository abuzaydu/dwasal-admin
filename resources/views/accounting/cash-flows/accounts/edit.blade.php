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
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Accounting</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right">

            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-xl-8 mx-auto">  
            <div class="card">
                <div class="card-body">
                    <div class="p-2 border rounded">
                        <form class="my-form" method="POST" action="{{route('accounts.update', encrypt($acc->id))}}">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="row pt-2">
                                <label for="account" class="col-sm-4 form-label">Account Type <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <select class="form-select form-select-sm mb-1" name="type" required>
                                        <option>{{$acc->type}}</option>
                                        <option value="">Select Type</option>
                                        <option value="Cash">{{trans('navmenu.cash')}}</option>
                                        <option value="Bank">{{trans('navmenu.bank')}}</option>
                                        <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row pt-2">
                                <label for="account" class="col-sm-4 form-label">Account Number <br>(<span class="text-primary">If it is a bank Account</span>)</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm mb-1" id="account" name="account_number" value="{{$acc->account_number}}" placeholder="Account Number">
                                </div>
                            </div>

                            <div class="row pt-2">
                                <label for="account" class="col-sm-4 form-label">Account Name <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm mb-1" id="account" name="account_name" value="{{$acc->account_name}}" placeholder="Account Name" required>
                                </div>
                            </div>
                            <div class="row pt-2">
                                <label for="bank_name" class="col-sm-4 form-label">Bank Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm mb-1" id="bank_name" name="bank_name" value="{{$acc->bank_name}}" placeholder="Bank Name">
                                </div>
                            </div>
                            <div class="row pt-2">
                                <label for="bank_name" class="col-sm-4 form-label">Branch Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm mb-1" id="branch_name" name="branch_name" value="{{$acc->branch_name}}" placeholder="Branch Name">
                                </div>
                            </div>
                        
                            <div class="row pt-2">
                                <label for="swift_code" class="col-sm-4 form-label">Swift Code</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm mb-1" id="swift_code" name="swift_code" value="{{$acc->swift_code}}" placeholder="Swift Code">
                                </div>
                            </div>
                            <div class="row pt-2">
                                <label for="account" class="col-sm-4 form-label">Currency </label>
                                <div class="col-sm-8">
                                    <select class="form-select form-select-sm mb-1" name="currency">
                                        <option value="">-Select</option>
                                        @foreach($currencies as $curr)
                                        @if($acc->currency == $curr->code)
                                        <option selected>{{$curr->code}}</option>
                                        @else
                                        <option>{{$curr->code}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row g-3 pt-2">
                                <button type="submit" class="col-sm-6 btn btn-success btn-sm" id="btn-submit">Save</button>
                                <a href="{{ url('accounts') }}" class="col-sm-6 btn btn-warning btn-sm">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Modal -->
<div class="modal fade" id="actxModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="myModalLabel">New Account</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                
            </div>
    </div>
</div>
@endsection