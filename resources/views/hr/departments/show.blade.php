@extends('layouts.hr')
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
        <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-12">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-title mb-3">{{$dept->name}}</h6>
                    <p class="card-text text-muted">{{$dept->description}}</p>
                    <ul class="list-unstyled mb-0">
                        <li class="py-2"><span class="text-muted me-2 w90 d-inline-block">Head:</span>{{$head->fname}} {{$head->mname}} {{$head->lname}}</li>
                        <li class="py-2"><span class="text-muted me-2 w90 d-inline-block">Position:</span>{{$position->name}}</li>

                    </ul>
                </div>
            </div>
        </div>
        <div class="col-xxl-6 col-xl-7 col-lg-7 col-md-12">
            <div class="card">
                <div class="card-body mb-3">
                    <div class="row mb-3 ">
                        <form id="add-member" action="{{url('add-dept')}}" method="POST">
                            @csrf
                            <input type="text" value="{{$dept->id}}" hidden name="id">
                            <div class="col">
                                <label class="form-label">Add Member to Department</label>
                                <select name="employee" onchange="submit();" class="form-select form-select-sm mb-3"  >
                                    <option >--- Select ---</option>
                                    @foreach($employees as $employee)
                                    <option value="{{$employee->id}}">&#160;{{$employee->fname}}  {{$employee->lname}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="row mb-3">
                        <h6>Department Members</h6>
                        <table class="table table-hover align-middle mb-0 card-table">
                            <thead>
                                <th>S/No</th>
                                <th>Name</th>
                                <th>Remove</th>
                            </thead>
                            <tbody>
                                 @foreach($members as $key => $member)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$member->fname}} {{$member->mname}} {{$member->lname}}</td>
                                    <td><a href="{{route('remove-dept',['emp_id' => encrypt($member->id)  , 'dept_id' => encrypt($dept->id) ])}}"><i style="color: red;" class="fa fa-trash"></i></a></td>
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