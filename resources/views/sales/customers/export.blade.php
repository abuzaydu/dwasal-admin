@extends('layouts.app')
@section('page-styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/css/intlTelInput.css" />
    <link href="{{ asset('side/assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('side/assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
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
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right pt-0">
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content pt-2">
                        <div class="tab-pane fade show active" id="tab_2" role="tabpanel">
                            <div class="table-responsive">
                                <table id="export-customers" class="table table-striped dataTable table-bordered display nowrap" style="width:100%; font-size: 14px;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>{{trans('navmenu.customer_name')}}</th>
                                            <th>{{trans('navmenu.phone_number')}}</th>
                                            <th>{{trans('navmenu.email_address')}}</th>
                                            <th>{{trans('navmenu.category')}}</th>
                                            <th>{{trans('navmenu.postal_address')}}</th>
                                            <th>{{trans('navmenu.physical_address')}}</th>
                                            <th>{{trans('navmenu.street')}}</th>
                                            <th>{{trans('navmenu.tin')}} </th>
                                            <th>{{trans('navmenu.vrn')}} </th>
                                            <th>{{trans('navmenu.created_at')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($customers as $i => $customer)
                                        <?php 
                                            $cat_name = '';
                                            $category = App\Models\CustomerCategory::find($customer->customer_category_id);
                                            if (!is_null($category)) {
                                                $cat_name = $category->cat_name;
                                            }
                                        ?>
                                        
                                        <tr>
                                            <td>{{$customer->id}}</td>
                                            <td>{{$customer->name}}</td>
                                            <td>{{$customer->phone}}</td>
                                            <td>{{$customer->email}}</td>
                                            <td>{{$cat_name}}</td>
                                            <td>{{$customer->postal_address}}</td>
                                            <td>{{$customer->physical_address}}</td>
                                            <td>{{$customer->street}}</td>
                                            <td>{{$customer->tin}}</td>
                                            <td>{{$customer->vrn}}</td>
                                            <td>{{$customer->created_at}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div> 
                        </div>               
                    </div>
                </div>
            </div>
        </div>                
    </div>
    <!--end row-->
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('side/assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(function () {

            var userlang = "<?php echo app()->getLocale(); ?>";
            var languageUrl = "";
            if (userlang === 'en') {
                languageUrl = "{{ asset('side/assets/vendor/libs/English.json') }}";
            } else {
                languageUrl = "{{ asset('side/assets/vendor/libs/Swahili.json') }}";
            }

            //Exportable table
            var exporttable = $('#export-customers').DataTable({
                "scrollX": true,
                "order": [
                    [0, "asc"]
                ],
                'bInfo': true,
                buttons: [
                    'excel', 'pdf'
                ]
            });

            exporttable.buttons().container().appendTo('#export-customers_wrapper .col-md-6:eq(1)');

            var deltable = $('#del-multiple').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                'columnDefs': [{
                    'targets': 0,
                    'checkboxes': {
                        'selectRow': true
                    }
                }],
                'select': {
                    'style': 'multi'
                },
                // 'order': [[1, 'asc']]
            })
        });
    </script>
@endsection