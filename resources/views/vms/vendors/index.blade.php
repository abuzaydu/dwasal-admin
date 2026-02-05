@extends('layouts.vms')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-3">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Vehicle Management</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-3 text-right pt-0">
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#vendorModal"><i class="fa fa-user-plus"></i> New Vendor</button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class=" card radius-6">
                <!-- /.box-header -->
                <div class="card-body">
                    <table id="example1" class="table table-striped display nowrap" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Vendor Name</th>
                                <th>{{trans('navmenu.contact_number')}}.</th>
                                <th>{{trans('navmenu.email_address')}}</th>
                                <th>{{trans('navmenu.address')}}</th>
                                <th>{{trans('navmenu.created_at')}}</th>
                                <th>{{trans('navmenu.actions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendors as $i => $vendor)
                            <tr>
                                <td>{{$i+1}}</td>
                                <td><a href="{{route('vendors.show' , encrypt($vendor->id))}}">{{$vendor->vendor_name}}</a></td>
                                <td>{{$vendor->phone}}</td>
                                <td>{{$vendor->email}}</td>
                                <td>{{ $vendor->address}}</td>
                                <td>{{$vendor->created_at}}</td>
                                
                                <td>
                                    <a href="{{route('vendors.edit' , encrypt($vendor->id))}}">
                                        <i class="fa fa-edit" style="color: blue;"></i>
                                    </a> | 
                                    <form method="POST" action="{{route('vendors.destroy' , encrypt($vendor->id))}}" id="delete-form-{{$i}}" style="display: inline;"> 
                                        @csrf
                                        @method('DELETE')
                                        <a href="javascript:;" onclick="return confirmDelete({{$i}})">
                                        <i class="fa fa-trash" style="color: red;"></i>
                                        </a>                       
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


    <!-- Modal -->
    <div class="modal fade" id="vendorModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"> New Vendor</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form-validate" method="POST" action="{{route('vendors.store')}}">
                    <div class="modal-body row">
                    @csrf
                        <input type="hidden" name="vendor_for" value="Parts">
                        <div class="col-md-6 pt-2">
                              <label class="form-label">Vendor Name <span style="color: red;">*</span></label>
                              <input id="register-username" type="text" name="name" required placeholder="Please enter vendor name" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6 pt-2">
                              <label class="form-label">Mobile</label>
                              <input id="register-username" type="text" name="phone" placeholder="Please enter vendor mobile number" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6 pt-2">
                              <label class="form-label">Email Address</label>
                              <input id="register-email" type="text" name="email" placeholder="Please enter vendor email address" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6 pt-2">
                              <label class="form-label">Address</label>
                              <input id="address" type="text" name="address" placeholder="Please enter vendor address" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6 pt-2">
                            <label for="account" class="form-label">Account Number</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="account_number" name="account_number" placeholder="Account Number">
                        </div>
                        <div class="col-md-6 pt-2">
                            <label for="account" class="form-label">Account Name</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="account_name" name="account_name" placeholder="Account Name">
                        </div>
                        <div class="col-md-6 pt-2">
                            <label for="swift_code" class="form-label">Swift Code</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="swift_code" name="swift_code" placeholder="Swift Code">
                        </div>
                        <div class="col-md-6 pt-2">
                            <label for="bank_name" class="form-label">Bank Name</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="bank_name" name="bank_name" placeholder="Bank Name">
                        </div>
                        <div class="col-md-6 pt-2">
                            <label for="bank_name" class="form-label">Branch Name</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="branch_name" name="branch_name" placeholder="Branch Name">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn btn-success btn-sm">Save</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
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
            $('#example1').DataTable();
        })
    </script>
@endsection

<script>
    function showHideForm(elem) {
        var newform = document.getElementById('new-form');
        if (elem == 'show') {
            newform.style.display = 'block';
        }else{
            newform.style.display = 'none';
        }
    }

    function showHideImportForm(elem) {
        var newform = document.getElementById('new-import-form');
        if (elem == 'show') {
            newform.style.display = 'block';
        }else{
            newform.style.display = 'none';
        }
    }

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
