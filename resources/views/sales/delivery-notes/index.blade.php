@extends('layouts.app')
@section('page-styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/css/intlTelInput.css" />
    <link href="{{ asset('side/assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('side/assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script>
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
            alert(id)
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
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-header">
                    <form id="frm-example" action="{{url('delete-multiple-dnotes')}}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table id="del-multiple" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                                <thead style="font-weight: bold; font-size: 14;">
                                    <tr>
                                        <th>#</th>
                                        <th>{{trans('navmenu.delivery_note_no')}}</th>
                                        <th>{{trans('navmenu.customer_name')}}</th>
                                        <th>{{trans('navmenu.comments')}}</th>
                                        <th>{{trans('navmenu.created_at')}}</th>
                                        <th>{{trans('navmenu.last_updated')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dnotes as $index => $dnote)
                                    <tr>
                                        <td>{{$dnote->id}}</td>
                                        <td> {{ sprintf('%04d', $dnote->note_no)}}</td>
                                        <td><a href="{{ route('delivery-notes.show', encrypt($dnote->id)) }}">{{$dnote->name}}</a></td>
                                        <td>{{$dnote->comments}}</td>
                                        <td>{{$dnote->created_at}}</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $dnote->updated_at)->diffForHumans() }}</td>
                                        <td>
                                            <a href="{{ route('delivery-notes.show', encrypt($dnote->id)) }}">
                                                <i class="fa fa-detail"></i>
                                            </a> | 
                                            <a href="{{ route('delivery-notes.edit', encrypt($dnote->id)) }}">
                                                <i class="fa fa-edit" style="color: blue;"></i>
                                            </a> |
                                            <form id="delete-form-{{$index}}" method="POST" action="{{ route('delivery-notes.destroy', encrypt($dnote->id))}}" style="display: inline;">
                                                @csrf
                                                @method("DELETE")
                                                <a href="#" onclick="confirmDelete('<?php echo $index; ?>')"><i class="fa fa-trash" style="color: red;"></i></a>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>  
                            </table>
                        </div>
                        <button id="submitButton" class="btn btn-danger btn-sm">{{trans('navmenu.delete_selected')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('side/assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script>
        $(function(){
            var userlang = "<?php echo app()->getLocale(); ?>";
            var languageUrl = "";
            if (userlang === 'en') {
                languageUrl = "{{ asset('side/assets/vendor/libs/English.json') }}";
            } else {
                languageUrl = "{{ asset('side/assets/vendor/libs/Swahili.json') }}";
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