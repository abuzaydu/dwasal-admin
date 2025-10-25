@extends('layouts.app')
@section('page-styles')
  <!-- Application Vendor CSS URL -->
  <link rel="stylesheet" href="{{ asset('side/assets/cssbundle/summernote.min.css') }}">
    <link href="{{ asset('side/assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
    <script>
        function showHidenoteForm(elem) {
            var newform = document.getElementById('new-note-form');
            var newbtn = document.getElementById('new-note-btn');
            var itemlist = document.getElementById('note-list');
            var newtitle = document.getElementById('new-note-title');
            var listtitle = document.getElementById('note-list-title');
            if (elem == 'show') {
                newform.style.display = 'block';
                newtitle.style.display = 'block';
                newbtn.style.display = 'none';
                itemlist.style.display = 'none';
                listtitle.style.display = 'none';
            }else{
                newform.style.display = 'none';
                newtitle.style.display = 'none';
                newbtn.style.display = 'block';
                itemlist.style.display = 'block';
                listtitle.style.display = 'block';
            }
        }

        function confirmDelete(id){
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
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                <button type="button" id="new-note-btn" class="btn btn-primary btn-sm" onclick="showHidenoteForm('show')"><i class="bx bxs-plus-square"></i>New Note/Terms & Conditions</button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded" id="new-note-form" style="display: none;">
                        <form class="form row g-3" id="note-form" method="POST" action="{{route('invoice-notes.store')}}">
                            @csrf
                            <div class="col-md-4">
                                <label class="form-label">Note For<span style="color: red; font-weight: bold;">*</span></label>
                                <select name="used_in" class="form-select form-select-sm mb-1" required>
                                    <option value="">--Select--</option>
                                    <option>Invoice</option>
                                    <option>Quotation</option>
                                    <option>Proforma</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Note type <span style="color: red; font-weight: bold;">*</span></label>
                                <select name="note_type" class="form-select form-select-sm ">
                                    <option value="">--Select--</option>
                                    <option>Notes</option>
                                    <option>Terms & Conditions</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label"> Content <span style="color: red;">^</span></label>
                                <div class="summernote" id="note-content"></div>
                            </div>
                            <input type="hidden" name="content" id="content">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <button type="button" class="btn btn-warning btn-sm px-4 radius-30" onclick="showHideDevceForm('hide')">{{trans('navmenu.btn_cancel')}}</button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive" id="note-list">
                        <table id="notes" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Note For</th>
                                    <th>Note Type</th>
                                    <th>Content </th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notes as $key => $note)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$note->used_in}}</a></td>
                                    <td>{{$note->note_type}}</td>
                                    <td>{!! $note->content !!}</td>
                                    <td>
                                        <a href="{{route('invoice-notes.edit', encrypt($note->id))}}">
                                            <i class="fa fa-edit" style="color: blue;"></i>
                                        </a> | 
                                        <form method="POST" action="{{route('invoice-notes.destroy' , encrypt($note->id))}}" id="delete-form-{{$key}}" style="display: inline;"> 
                                            @csrf
                                            @method('DELETE')
                                            <a href="javascript:;" onclick="return confirmDelete({{$key}})">
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
    <!--end row-->
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('side/assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    
    <script>
        $(function () {
            //Exportable table
            $('#notes').DataTable({
                'scrollX': true
            });
        });
    </script>
    <script src="{{ asset('side/assets/js/bundle/summernote.bundle.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#note-content').summernote({
              toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']]
              ]
            });
            $('.note-editor .note-btn').on('click', function() {
                $(this).next().toggleClass("show");
            });

            $('#btn-submit').on('click', function(e){
                e.preventDefault();
                var content = $('#note-content').summernote('code');
                $('#content').val(content);
                $('#note-form').submit();
            })
        });
    </script>
@endsection