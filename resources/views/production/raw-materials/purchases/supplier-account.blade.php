@extends('layouts.prod')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
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
            <div class="col-lg-4 col-md-4 col-sm-4">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-7 col-sm-4 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class=" card radius-6">
                <div class="card-body">
                    <div class="p-4 border rounded" id="new-form" style="display: none;">
                        <form class="form" method="POST" action="{{route('suppliers.store')}}">
                            @csrf
                            <input type="hidden" name="supplier_for" value="Raw Materials">
                            <div class="col-sm-4" >
                                <label for="name" class="form-label">{{trans('navmenu.supplier_name')}}</label>
                                <input type="text" name="name" class="form-control form-control-sm mb-1" required placeholder="{{trans('navmenu.hnt_supplier_name')}}">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">{{trans('navmenu.contact_number')}}</label>
                                <input id="phone" type="tel" name="contact_no" placeholder="{{trans('navmenu.hnt_contact_number')}}  Eg. 0789XXXXXX" class="form-control form-control-sm mb-1" data-inputmask='"mask": "9999999999"' data-mask>
                            </div> 
                            <div class="col-sm-4">
                                <label class="form-label">{{trans('navmenu.email_address')}}</label>
                                <input type="text" name="email" placeholder="{{trans('navmenu.hnt_supplier_email')}}" class="form-control form-control-sm mb-1">
                            </div>  
                            <div class="col-sm-4">
                                <label class="form-label">{{trans('navmenu.address')}}</label>
                                <input type="text" name="address" placeholder="{{trans('navmenu.address')}}" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-4 pt-2">
                                <button type="submit" class="btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <button type="reset" class="btn btn-warning btn-sm">{{trans('navmenu.btn_reset')}}</button>
                            </div>
                        </form>
                    </div>
                    <div id="items-list">
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
                                    <td><a href="{{route('rm-suppliers-transaction.show' , encrypt($supplier->id))}}">{{$supplier->name}}</a></td>
                                    <td>{{$supplier->contact_no}}</td>
                                    <td>{{$supplier->email}}</td>
                                    <td>{{ $supplier->address}}</td>
                                    <td>{{$supplier->created_at}}</td>
                                    <td>
                                        <a href="{{route('suppliers.edit' , encrypt($supplier->id))}}">
                                            <i class="fa fa-edit" style="color: blue;"></i>
                                        </a>
                                        |
                                        <form method="POST" action="{{route('suppliers.destroy' , encrypt($supplier->id))}}" id="delete-form-{{$i}}" style="display: inline;"> 
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
    </div>
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>
    <script>
        $(function () {

            var userlang = "<?php echo app()->getLocale(); ?>";
            var languageUrl = "";
            if (userlang === 'en') {
                languageUrl = "{{ asset('assets/vendor/libs/English.json') }}";
            } else {
                languageUrl = "{{ asset('assets/vendor/libs/Swahili.json') }}";
            }

            //Exportable table
            $('#example1').DataTable({
                "scrollX": true,
            });
        });
    </script>
@endsection