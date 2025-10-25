@extends('layouts.hr')
    <script type="text/javascript">
       

    </script>
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('hr-departments')}}">Department</a></li>
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
                        <div class="psetting-relative">
                            <h6 class="mb-0 text-uppercase" id="new-title" style="display: none;"></h6>
                            <h6 class="mb-0 text-uppercase" id="list-title">Department List</h6>
                        </div>
                        <div class="ms-auto">
                            <button type="button" id="new-btn" class="btn btn-primary" onclick="showHideForm('show')"><i class="bx bxs-plus-square"></i>Add Departiment</button>
                        </div>
                    </div>

                    <div class="p-4 mb-3 border rounded" id="new-form" style="display: none;">
                        <form class="row g-3" id="basic-form" novalidate method="POST" action="{{ route('hr-departments.store') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="name" class="form-label">Name<span style="color: red;">*</span></label>
                                    <input type="text" name="name" id="validationCustom03" class="form-control form-control-sm mb-3" placeholder="Name" required>
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please provide a Name.</div>
                                </div>
                                <div class="col-md-4">
                                  <label for="description" class="form-label">Description</label>
                                  <input type="text" name="description" id="validationCustom03" class="form-control form-control-sm mb-3" placeholder="Description">
                                  <div class="valid-feedback">Looks good!</div>
                                  <div class="invalid-feedback">Please provide a description</div>
                                </div>
                                <div class="col-md-4">
                                  <label for="head" class="form-label">Head of Department <span style="color: red;">*</span></label>
                                  <select name="head" class="form-select form-select-sm mb-3" required>
                                      <option value="">--- Select ---</option>
                                      @foreach($employees as $employee)
                                      <option value="{{$employee->id}}">&#160;{{$employee->fname}}  {{$employee->lname}}</option>
                                      @endforeach
                                  </select>
                                  <div class="valid-feedback">Looks good!</div>
                                  <div class="invalid-feedback">Please provide Head of Department.</div>
                                </div>
                            </div>   
                            <div class="col-12">
                                <button class="btn btn-primary px-4 radius-30" type="submit">Save</button>
                                <a onclick="showHideForm('hide')" class="btn btn-warning px-4 radius-30">Cancel</a>
                            </div>
                        </form>
                    </div>

                    <div class="p-4 mb-3  border rounded" id="edit-form" style="display: none;">
                        <form class="row g-3" id="basic-form" novalidate method="POST" action="{{ route('hr-departments.update' , encrypt(1)) }}">
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
                                  <label for="description" class="form-label">Description</label>
                                  <input type="text" name="e_description" id="validationCustom03" class="form-control form-control-sm mb-3" placeholder="Description">
                                  <div class="valid-feedback">Looks good!</div>
                                  <div class="invalid-feedback">Please provide a description</div>
                                </div>
                                <div class="col-md-4">
                                  <label for="marital_status" class="form-label">Head of Department <span style="color: red;">*</span></label>
                                  <select name="e_head" class="form-select form-select-sm mb-3" required>
                                      @foreach($employees as $employee)
                                      <option value="{{$employee->id}}">{{$employee->fname}} {{$employee->lname}}</option>
                                      @endforeach
                                  </select>
                                  <div class="valid-feedback">Looks good!</div>
                                  <div class="invalid-feedback">Please provide Head of Department.</div>
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
                                    <th>Name</th>
                                    <th>Head of Dept</th>
                                    <th>Total Members</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($depts as $key => $dept)
                                <tr>
                                    <th scope="row">{{$key+1}}</th>
                                    <td>{{ $dept->name }}</td>
                                    <td>{{ $dept->fname}} {{ $dept->lname }}</td>
                                    <td>{{$dept->employees()->count()}}</td>
                                    <td>
                                        <a href="{{ route('hr-departments.show', encrypt($dept->id)) }}" class="text-secondary"><i class='fa fa-eye mr-1'></i>View</a> | 
                                        <a href="javascript:;" onclick="EditDept({{$dept}})"><i class='fa fa-pencil mr-1'></i>Edit</a> | 
                                        <form id="delete-form-{{$key}}" method="POST" action="{{ route('hr-departments.destroy', encrypt($dept->id)) }}" style="display: inline;">
                                            @csrf
                                            @method("DELETE")
                                            <a href="javascript:;" class="text-danger" onclick="return confirmDelete('<?php echo $key; ?>')" class="text-danger"><i class='fa fa-trash mr-1'></i>Delete</a>
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

    function EditDept(data){
        document.getElementById('new-form').style.display = 'none';
        document.getElementById('edit-form').style.display = 'block';

        let dept = data;

        document.getElementsByName('e_name')[0].value = dept.name;
        document.getElementsByName('e_description')[0].value = dept.description;
        document.getElementsByName('id')[0].value = dept.id;
        document.getElementsByName('e_head')[0].value = dept.leader_id;

    }

</script>
@endsection