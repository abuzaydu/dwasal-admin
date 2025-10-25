@extends('layouts.hr')
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('hr-holidays')}}">Holidays</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end">
                
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="d-lg-flex align-items-center mb-4 gap-3">
                        <div class="ms-auto">
                            <button type="button" id="new-btn" class="btn btn-primary" onclick="showHideForm('show')"><i class="bx bxs-plus-square"></i>Add Holiday</button>
                        </div>
                    </div>

                    <div class="p-4 mb-3 border rounded" id="new-form" style="display: none;">
                        <form class="row g-3" id="basic-form" novalidate method="POST" action="{{ route('hr-holidays.store') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="name" class="form-label">Name<span style="color: red;">*</span></label>
                                    <input type="text" name="name" id="validationCustom03" class="form-control form-control-sm mb-3" placeholder="Name" required>
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please provide a Name.</div>
                                </div>
                                <div class="col-md-4">
                                  <label for="description" class="form-label">Date</label>
                                  <input type="date" name="date" id="validationCustom03" class="form-control form-control-sm mb-3" required >
                                </div>

                                <div class="col-md-4">
                                  <label for="description" class="form-label">Is Recurring Holidays</label>
                                  <input type="checkbox" name="rec" id="validationCustom03" class="pt-3">
                                </div>
                            </div>   
                            <div class="col-12">
                                <button class="btn btn-primary px-4 radius-30" type="submit">Save</button>
                                <a onclick="showHideForm('hide')" class="btn btn-warning px-4 radius-30">Cancel</a>
                            </div>
                        </form>
                    </div>

                    <div class="p-4 mb-3  border rounded" id="edit-form" style="display: none;">
                        <form class="row g-3" id="basic-form" novalidate method="POST" action="{{ route('hr-holidays.update' , encrypt(1)) }}">
                            @csrf
                            @method('PUT')
                            <input type="text" name="id" value="" hidden>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="name" class="form-label">Name<span style="color: red;">*</span></label>
                                    <input type="text" name="e_name" id="validationCustom03" class="form-control form-control-sm mb-3" placeholder="Name" required>
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please provide a Name.</div>
                                </div>
                                <div class="col-md-4">
                                  <label for="description" class="form-label">Date</label>
                                  <input type="text" name="e_date" id="validationCustom03" class="form-control form-control-sm mb-3" required >
                                </div>
                                <div class="col-md-4">
                                  <label for="description" class="form-label">Is Recurring Holidays</label>
                                  <input type="checkbox" name="e_rec" id="validationCustom03" class="pt-3"  >
                                </div>
                            </div>   
                            <div class="col-12">
                                <button class="btn btn-primary px-4 radius-30" type="submit">Save</button>
                                <a onclick="showHideForm('hide')" class="btn btn-warning px-4 radius-30">Cancel</a>
                            </div>
                        </form>
                    </div>

                    <div id="item-list">
                        <table class="table myDataTable table-hover align-middle mb-0 card-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Holiday Name</th>
                                    <th>Day</th>
                                    <th>Date</th>
                                    <th>Recurring</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($holidays as $key => $holiday)
                                <tr>
                                    <th scope="row">{{$key+1}}</th>
                                    <td>{{ $holiday->name}}</td>
                                    <td>{{ Carbon\Carbon::parse($holiday->date)->isoFormat('dddd')}}</td>
                                    <td>{{ Carbon\Carbon::parse($holiday->date)->isoFormat('Do MMMM YYYY')}}</td>
                                    @if($holiday->is_recurring)
                                    <td>Yes</td>
                                    @else
                                    <td>No</td>
                                    @endif
                                    <td>
                                        <a onclick="EditHoliday({{$holiday}})"><i class='bx bx-pencil mr-1'></i>Edit</a> | 
                                        <form id="delete-form-{{$key}}" method="POST" action="{{ route('hr-holidays.destroy', encrypt($holiday->id)) }}" style="display: inline;">
                                            @csrf
                                            @method("DELETE")
                                            <a class="text-danger" onclick="return confirmDelete('<?php echo $key; ?>')" class="text-danger"><i class='bx bx-trash mr-1'></i>Delete</a>
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
<link rel="stylesheet" href="{{ asset('assets/css/DatePickerX.css') }}">
    <script src="{{ asset('assets/js/DatePickerX.min.js') }}"></script>
    <script type="text/javascript">
        window.addEventListener('DOMContentLoaded', function()
        {
            var date = document.querySelector('[name="date"]');
            var e_date = document.querySelector('[name="e_date"]');

             date.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                // maxDate    : new Date()
            });

            e_date.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                // maxDate    : new Date()
            });
             
           
        });
    </script>
<script type="text/javascript">
     function showHideForm(elem) {
        var newform = document.getElementById('new-form');
        var editform = document.getElementById('edit-form');

        if (elem == 'show') {
                newform.style.display = 'block';
        }else{
            newform.style.display = 'none';
            editform.style.display = 'none';
        }
    }

    function confirmDelete(id) {
        Swal.fire({
            title: "Sure you want to delete ",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "delete",
            cancelButtonText: "cancel"
        }).then((result) => {
            if (result.value) {
                document.getElementById('delete-form-' + id).submit();
                Swal.fire(
                    "Deleted",
                    'success'
                )
            }else{
                Swal.fire(
                    "Cancelled",
                    'success'
                )
            }
        })
    }

    function EditHoliday(data){
        document.getElementById('new-form').style.display = 'none';
        document.getElementById('edit-form').style.display = 'block';

        let holiday = data;
       
        document.getElementsByName('e_name')[0].value = holiday.name;
        document.getElementsByName('e_date')[0].value = holiday.date;
        if(holiday.is_recurring){
            document.getElementById('e_rec')[0].checked = true;
             alert(holiday.is_recurring);
            
         }

        document.getElementsByName('id')[0].value = holiday.id;

    }

</script>
@endsection