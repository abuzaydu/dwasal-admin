@extends('layouts.prod')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script type="text/javascript">
    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_delete')}}",
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

    function submitEditForm(key) {
        document.getElementById('edit-form-' + key).submit();
    }

</script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-xl-12 mx-auto ">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7 d-flex align-items-center">
                            <div class="d-flex align-items-end px-1 py-1">
                                <ul class="nav nav-tabs nav-primary" role="tablist"  >
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#tab_1" role="tab" aria-selected="false">
                                            <div class="d-flex align-items-center">
                                                <div class="tab-icon"><i class='fa fa-list-check font-18 me-1'></i></div>
                                                <div class="tab-title">Product Pricing Calculators</div>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" href="{{ route('product-pricings.create')}}" aria-selected="false">
                                            <div class="d-flex align-items-center">
                                                <div class="tab-icon"><i class='fa fa-list-plus font-18 me-1'></i></div>
                                                <div class="tab-title">New Pricing Calculator</div>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <form class="dashform" action="{{ url('f-product-pricings') }}" method="POST" id="stockform">
                                @csrf
                                <div class="col-md-12">
                                    <select name="year" class="form-select form-select-sm me-1">
                                        <option>2024</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="tab-content py-1">
                        <div class="tab-pane fade show active table-responsive" id="tab_1" role="tabpanel">
                            <table id="del-multiple" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{trans('navmenu.date')}}</th>
                                        <th>{{trans('navmenu.name')}}</th>
                                        <th>{{trans('navmenu.status')}}</th>
                                        <th>Created BY</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pricings as $key => $item)
                                    <tr>
                                        <td>{{$item->id}}</td>
                                        <td>{{date('d-m-Y', strtotime($item->date))}}</td>
                                        <td><a href="{{ route('product-pricings.show', encrypt($item->id)) }}">{{$item->name}}</a></td>
                                        <td>
                                            @if($item->is_pending)
                                            PENDING
                                            @else
                                            COMPLETED
                                            @endif
                                        </td>
                                        <td>{{$item->first_name}} {{$item->last_name}}</td>
                                        <td> 
                                            <form id="edit-form-{{$key}}" action="{{ url('product-pricings/edit')}}" method="POST" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="id" value="{{$item->id}}">
                                                <a href="javascript:;" onclick="submitEditForm('<?php echo $key; ?>')" class="text-primary" title="Edit"><i class="fa fa-pencil"></i></a> | 
                                            </form>
                                            <form id="delete-form-{{$key}}" method="POST" action="{{ route('product-pricings.destroy', encrypt($item->id))}}" style="display: inline;"> 
                                                @csrf
                                                @method("DELETE")
                                                <a href="javascript:;" class="text-danger" onclick=" return confirmDelete('<?php echo $key; ?>')"><i class='fa fa-trash'></i></a>
                                            </form>  
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <form id="frm-example" action="{{url('delete-multiple-pricings')}}" method="POST">
                                @csrf
                                <button id="submitButton" class="btn btn-danger">{{trans('navmenu.delete_selected')}}</button>
                            </form>
                        </div>
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
        $(function(){
            var userlang = "<?php echo app()->getLocale(); ?>";
            var languageUrl = "";
            if (userlang === 'en') {
                languageUrl = "{{ asset('assets/vendor/libs/English.json') }}";
            } else {
                languageUrl = "{{ asset('assets/vendor/libs/Swahili.json') }}";
            }

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

            var counterChecked = 0;
            $('#submitButton').prop("disabled", true);

            $('body').on('change', 'input[type="checkbox"]', function() {
                this.checked ? counterChecked++ : counterChecked--;
                counterChecked > 0 ? $('#submitButton').prop("disabled", false) : $('#submitButton').prop(
                    "disabled", true);
                counterChecked < 0 ? counterChecked = 0 : counterChecked;
                console.log(counterChecked);
            });

            $('#submitButton').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "{{ trans('navmenu.are_you_sure_delete') }}",
                    text: "{{ trans('navmenu.no_revert') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
                    cancelButtonText: "{{ trans('navmenu.no') }}"
                }).then((result) => {
                    if (result.value) {
                        $('#frm-example').submit();
                        Swal.fire(
                            "{{ trans('navmenu.deleted') }}",
                            "{{ trans('navmenu.cancelled') }}",
                            'success'
                        )
                    }
                })
            });

              // Handle form submission event 
            $('#frm-example').on('submit', function(e) {
                var form = this;
                var rows_selected = deltable.column(0).checkboxes.selected();
                if (rows_selected.length > 0) {
                    // Iterate over all selected checkboxes
                    $.each(rows_selected, function(index, rowId) {
                        // Create a hidden element 
                        $(form).append(
                            $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', 'ids[]')
                            .val(rowId)
                        );
                    });
                }
            });
        });
    </script>
@endsection