@extends('layouts.app')
@section('page-styles')
    <!-- Application Vendor CSS URL -->
    <link rel="stylesheet" href="{{ asset('side/assets/cssbundle/summernote.min.css') }}">
@endsection

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
                    <div class="p-4 border rounded">
                        <form class="form row g-3" id="note-form" method="POST" action="{{route('invoice-notes.update', encrypt($note->id))}}">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-4">
                                <label class="form-label">Note For<span style="color: red; font-weight: bold;">*</span></label>
                                <select name="used_in" class="form-select form-select-sm mb-1" required>
                                    <option>{{$note->used_in}}</option>
                                    <option value="">--Select--</option>
                                    <option>Invoice</option>
                                    <option>Quotation</option>
                                    <option>Proforma</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Note type <span style="color: red; font-weight: bold;">*</span></label>
                                <select name="note_type" class="form-select form-select-sm ">
                                    <option>{{$note->note_type}}</option>
                                    <option value="">--Select--</option>
                                    <option>Notes</option>
                                    <option>Terms & Conditions</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label"> Content <span style="color: red;">^</span></label>
                                <div class="summernote" id="note-content">
                                    {!! $note->content !!}
                                </div>
                            </div>
                            <input type="hidden" name="content" id="content">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <a href="{{ url('invoice-notes') }}" class="btn btn-warning btn-sm px-4 radius-30">{{trans('navmenu.btn_cancel')}}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection

@section('page-scripts')
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