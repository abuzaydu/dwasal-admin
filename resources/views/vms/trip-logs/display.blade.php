@extends('layouts.vms')  
@section('content')
    <table class="table table-striped table-bordered datatable" id="expensesTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Trip No</th>
                <th>Driver</th>
                <th>Vehicle</th>
                <th>Trip Type</th>
                <th>Exp Group</th>
                <th>Date</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
            <tr>
                <td>{{ $expense->id }}</td>
                <td>{{ $expense->trip_no }}</td>
                <td>{{ trim(($expense->employee->fname ?? '') . ' ' . ($expense->lname ?? '')) ?: 'N/A' }}</td>
                <td>{{ $expense->vehicle->plate_no ?? 'N/A' }} {{ $expense->vehicle_name ? '- ' . $expense->vehicle_name : '' }}</td>
                <td>{{ $expense->trip_type ?? 'N/A' }}</td>
                <td>{{ $expense->exp_group ?? '-' }}</td>
                <td>{{ $expense->date }}</td>
                <td>{{ number_format($expense->items->sum('total_price'), 2) }}</td>
                <td>
                    @if($expense->status === 'Pending')
                        <span class="badge bg-warning text-dark">Pending</span>
                    @elseif($expense->status === 'Awaiting For Approval')
                        <span class="badge bg-info">Awaiting Approval</span>
                    @elseif($expense->status === 'In Progress')
                        <span class="badge bg-primary">In Progress</span>
                    @elseif($expense->status === 'Approved')
                        <span class="badge bg-success">Approved</span>
                    @elseif($expense->status === 'Rejected')
                        <span class="badge bg-danger">Rejected</span>
                    @elseif($expense->status === 'Closed')
                        <span class="badge bg-secondary">Closed</span>
                    @else
                        <span class="badge bg-light text-dark">{{ $expense->status }}</span>
                    @endif
                </td>
            <td>
                    <a href="{{ route('vms-expenses.show', encrypt($expense->id)) }}" class="text-info">
                        <i class="fa fa-eye"></i>
                    </a>

                    @if(in_array($expense->status, ['Awaiting For Approval', 'Rejected']))
                        <a href="{{ route('vms-expenses.edit', encrypt($expense->id)) }}" class="text-primary ms-2">
                            <i class="fa fa-edit"></i>
                        </a>
                    @endif

                    @if($expense->status == 'Awaiting For Approval')
                        <a href="javascript:;" onclick="confirmDeleteExpense('{{ $expense->id }}')" class="ms-2">
                            <i class="fa fa-trash text-danger"></i>
                        </a>

                        <form id="deleteExpenseForm_{{ $expense->id }}" method="POST"
                            action="{{ route('vms-expenses.destroy', encrypt($expense->id)) }}"
                            style="display:none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
            </td>
            </tr>
            @endforeach
        </tbody>
 </table>
@endsection