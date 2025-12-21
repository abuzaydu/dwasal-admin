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
                <a href="javascript:;" onclick="showHideForm('show')" class="btn btn-primary btn-sm">New Vendor</a>
                <a href="javascript:;" onclick="showHideImportForm('show')" class="btn btn-warning btn-sm">Import From File</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        @if(Auth::user()->can('create-vendor'))
        <div class="col-md-12 mx-auto" id="new-form" style="display: none;">
            <div class="card radius-6">
                <div class="card-body">
                    <form class="form row g-1" method="POST" action="{{route('vendors.store')}}">
                        @csrf
                        <input type="hidden" name="vendor_for" value="Stock">
                        <div class="col-sm-3" >
                            <label for="name" class="form-label">Vendor Name</label>
                            <input type="text" name="name" class="form-control form-control-sm mb-1" required placeholder="{{trans('navmenu.hnt_vendor_name')}}"> 
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">{{trans('navmenu.contact_number')}}</label>
                            <input id="phone" type="tel" name="phone" placeholder="{{trans('navmenu.hnt_contact_number')}}  Eg. 0789XXXXXX" class="form-control form-control-sm mb-1" data-inputmask='"mask": "9999999999"' data-mask>
                        </div> 
                        <div class="col-sm-3">
                            <label class="form-label">{{trans('navmenu.email_address')}}</label>
                            <input type="text" name="email" placeholder="{{trans('navmenu.hnt_vendor_email')}}" class="form-control form-control-sm mb-1">
                        </div>  
                        <div class="col-sm-3">
                            <label class="form-label">{{trans('navmenu.address')}}</label>
                            <input type="text" name="address" placeholder="{{trans('navmenu.address')}}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-sm-3">
                            <label for="account" class="form-label">Account Number</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="account" name="account_number" placeholder="Account Number">
                        </div>
                        <div class="col-sm-3">
                            <label for="account" class="form-label">Account Name</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="account" name="account_name" placeholder="Account Name">
                        </div>
                        <div class="col-sm-3">
                            <label for="swift_code" class="form-label">Swift Code</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="swift_code" name="swift_code" placeholder="Swift Code">
                        </div>
                        <div class="col-sm-3">
                            <label for="bank_name" class="form-label">Bank Name</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="bank_name" name="bank_name" placeholder="Bank Name">
                        </div>
                        <div class="col-sm-3">
                            <label for="bank_name" class="form-label">Branch Name</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="branch_name" name="branch_name" placeholder="Branch Name">
                        </div>
                        <input type="hidden" name="vendor_for" value="Stock">
                        <div class="col-sm-3 pt-2">
                            <button type="submit" class="btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                            <button type="reset" class="btn btn-warning btn-sm" onclick="showHideForm('hide')">{{trans('navmenu.btn_cancel')}}</button>
                        </div>
                    </form>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-12 mx-auto" id="new-import-form" style="display: none;">
            <div class="card radius-6">
                <div class="card-body">
                    <form class="row g-3 needs-validation" novalidate method="POST" action="{{url('import-vendors')}}"  enctype="multipart/form-data">
                        @csrf
                        <div class="col-sm-6">
                            <h3>Instruction to Import vendors</h3>
                            <p>Please download the sample excel file below then use it to create your vendors list excel file then Save it to your PC.</p>
                            <p>Then Click  Browse to fetch your file then click Upload to import.</p>
                        </div>
                        <div class="col-sm-6">
                            <h3>Download Sample Excel file</h3>
                            <a href="{{ url('sample-vendors') }}" class="btn btn-primary btn-sm"><i class="fa fa-download"></i> Download Sample</a><br><br>
                            <div class="form-group">
                                <label for="exampleInputFile" class="form-label">Choose Customers excel file</label>
                                <input type="file" class="form-control" id="exampleInputFile" name="file" required>
                                @if ($errors->has('file'))
                                <span class="help-block" style="color: red;">
                                    <strong>{{ $errors->first('file') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group" style="margin-top: 5px;">
                                <button type="submit" class="btn btn btn-success btn-sm"><i class="fa fa-upload"></i> Upload</button>
                                <a href="#" onclick="showHideImportForm('hide')" class="btn btn-warning btn-sm"><i class="fa fa-x"></i> Cancel </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div> 
        @endif

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
