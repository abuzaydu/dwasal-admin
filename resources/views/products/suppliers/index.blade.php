@extends('layouts.inv')

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
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-3 text-right pt-0">
                <a href="javascript:;" onclick="showHideForm('show')" class="btn btn-primary btn-sm">{{trans('navmenu.add_new_supplier')}}</a>
                <a href="javascript:;" onclick="showHideImportForm('show')" class="btn btn-warning btn-sm">Import From File</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        @if(Auth::user()->can('create-supplier'))
        <div class="col-md-12 mx-auto" id="new-form" style="display: none;">
            <div class="card radius-6">
                <div class="card-body">
                    <form class="form row g-1" method="POST" action="{{route('suppliers.store')}}">
                        @csrf
                        <input type="hidden" name="supplier_for" value="Stock">
                        <div class="col-sm-3" >
                            <label for="name" class="form-label">{{trans('navmenu.supplier_name')}}</label>
                            <input type="text" name="name" class="form-control form-control-sm mb-1" required placeholder="{{trans('navmenu.hnt_supplier_name')}}"> 
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">{{trans('navmenu.contact_number')}}</label>
                            <input id="phone" type="tel" name="contact_no" placeholder="{{trans('navmenu.hnt_contact_number')}}  Eg. 0789XXXXXX" class="form-control form-control-sm mb-1" data-inputmask='"mask": "9999999999"' data-mask>
                        </div> 
                        <div class="col-sm-3">
                            <label class="form-label">{{trans('navmenu.email_address')}}</label>
                            <input type="text" name="email" placeholder="{{trans('navmenu.hnt_supplier_email')}}" class="form-control form-control-sm mb-1">
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
                        <input type="hidden" name="supplier_for" value="Stock">
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
                    <form class="row g-3 needs-validation" novalidate method="POST" action="{{url('import-suppliers')}}"  enctype="multipart/form-data">
                        @csrf
                        <div class="col-sm-6">
                            <h3>Instruction to Import Suppliers</h3>
                            <p>Please download the sample excel file below then use it to create your suppliers list excel file then Save it to your PC.</p>
                            <p>Then Click  Browse to fetch your file then click Upload to import.</p>
                        </div>
                        <div class="col-sm-6">
                            <h3>Download Sample Excel file</h3>
                            <a href="{{ url('sample-suppliers') }}" class="btn btn-primary btn-sm"><i class="fa fa-download"></i> Download Sample</a><br><br>
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
                                <th>{{trans('navmenu.supplier_name')}}</th>
                                <th>{{trans('navmenu.contact_number')}}.</th>
                                <th>{{trans('navmenu.email_address')}}</th>
                                <th>{{trans('navmenu.address')}}</th>
                                <th>{{trans('navmenu.created_at')}}</th>
                                <th>{{trans('navmenu.actions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suppliers as $i => $supplier)
                            <tr>
                                <td>{{$i+1}}</td>
                                <td><a href="{{route('suppliers.show' , encrypt($supplier->id))}}">{{$supplier->name}}</a></td>
                                <td>{{$supplier->contact_no}}</td>
                                <td>{{$supplier->email}}</td>
                                <td>{{ $supplier->address}}</td>
                                <td>{{$supplier->created_at}}</td>
                                
                                <td>
                                    @if(Auth::user()->can('edit-supplier'))
                                    <a href="{{route('suppliers.edit' , encrypt($supplier->id))}}">
                                        <i class="fa fa-edit" style="color: blue;"></i>
                                    </a>
                                    @endif | 
                                    @if(Auth::user()->can('delete-supplier'))
                                    <form method="POST" action="{{route('suppliers.destroy' , encrypt($supplier->id))}}" id="delete-form-{{$i}}" style="display: inline;"> 
                                        @csrf
                                        @method('DELETE')
                                        <a href="javascript:;" onclick="return confirmDelete({{$i}})">
                                        <i class="fa fa-trash" style="color: red;"></i>
                                        </a>                       
                                    </form> 
                                    @endif     
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
