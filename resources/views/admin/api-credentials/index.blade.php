@extends('layouts.adm')
<script>
    function confirmDelete(id) {
        Swal.fire({
          title: "Are you sure you want to Delete this record?",
          text: "The Account will no longer be accessible please be certain",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes, Delete",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('delete-form-'+id).submit();
            Swal.fire(
              "Deleted",
              "Deleted",
              'success'
            )
          }
        })
    }

    function confirmCreate(id) {
        Swal.fire({
          title: "Are you sure you want to create a new JWT Toke for this account?",
          text: "Old Token will no longer be valid. please be certain",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes, Create",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href = "{{ url('admin/create-new-token/') }}/" + id;
            Swal.fire(
              "Created",
              "Created",
              'success'
            )
          }
        })
    }
</script>
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Accounts & Users</li>       
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-2 rounded">
                         <form class="form row g-1" method="post" action="{{ route('payment-auths.store') }}" validate>
                            {{ csrf_field() }}
                            <div class="col-md-3">
                                <label class="form-label">Business <span style="color: red;">*</span> </label>
                                <select name="shop_id" required="required"
                                    class="form-select form-select-sm mb-1 border-primary mb-1">
                                    <option value="">Select Business</option>
                                    @foreach ($shops as $key => $shop)
                                        <option value="{{ $shop->id }}">{{ $shop->name }} ({{ $shop->mobile }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Merchant MSISDN <span style="color: red;">*</span> </label>
                                <input class="form-control form-control-sm mb-1 border-primary" account="text" name="merchant_msisdn" placeholder="Enter Merchant msisdn 255XXXXXXXXX" id="userinput8" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Username <span style="color: red;">*</span> </label>
                                <input class="form-control form-control-sm mb-1 border-primary" account="text" name="username" placeholder="Enter Username" id="userinput8" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Password <span style="color: red;">*</span> </label>
                                <input class="form-control form-control-sm mb-1 border-primary" type="password" name="password" placeholder="Enter Password" required>
                            </div>
                            <div class="col-md-12">
                                <label>Collection Account</label>
                            </div>
                            <hr>
                            <div class="col-md-3">
                                <label class="form-label">Account Type <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm mb-1" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="Bank">{{trans('navmenu.bank')}}</option>
                                    <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Account Number</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="account" name="account_number" placeholder="Account Number">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Account Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm mb-1" id="account" name="account_name" placeholder="Account Name" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Bank Name/MNO Channel</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="bank_name" name="bank_name" placeholder="Bank Name">
                            </div>
                            <div class="col-md-12 pt-0">
                                <button account="submit" class="btn btn-primary btn-sm">Save</button>
                            </div>
                        </form>
                    </div>
                    <table id="payment-auths" class="table table-striped" style="width: 100%;">
                        <thead style="font-weight: bold; font-size: 14;">
                            <tr>
                                <th style="width: 10px;">#</th>
                                <th>Business name</th>
                                <th>MSISDN</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payauths as $key => $account)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $account->name }}</td>
                                <td>{{ $account->merchant_msisdn }}</td>
                                <td>{{ $account->username }}</td>
                                <td>{{ $account->passhint }} </td>
                                <td>
                                    <a href="#" onclick="confirmCreate('<?php echo encrypt($account->id); ?>')"><i class="fa fa-plus"></i> Create New JWT Token</a>
                                    <a href="{{ route('payment-auths.show', encrypt($account->id))}}" style="color: gray;"><i class="fa fa-eye"></i> Show Details</a>
                                    <a href="{{ route('payment-auths.edit', encrypt($account->id)) }}">
                                        <i class="fa fa-edit"></i>
                                    </a> |
                                    <form id="delete-form-{{ $key }}" method="POST" action="{{ route('payment-auths.destroy', encrypt($account->id)) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <a href="#" class="button" onclick="confirmDelete('{{ $key }}')"><i class="fa fa-trash" style="color: red;"></i></a>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(function(){
            $('#payment-auths').DataTable({
                "scrollX": true,
            });
        });
    </script>
@endsection
