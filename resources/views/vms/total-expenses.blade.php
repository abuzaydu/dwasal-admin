@extends('layouts.vms')
@section('content')
<div class="block-header pt-4">
    <div class="row">
        <div class="col-sm-12">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ url('f-vehicles-dash') }}">VMS Dashboard</a></li>
                <li class="breadcrumb-item active">{{ $page }}</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header"
                style="background: #f8f9fa; border-bottom: 2px solid #dc3545;">
                <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                    <h6 style="margin: 0; font-weight: 600; color: #dc3545;">
                        <i class="fa fa-money me-2"></i>{{ $page }}
                    </h6>
                    <form method="GET" action="{{ url('vms/total-expenses') }}"
                        style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <input type="date" name="start_date" value="{{ $start_date }}"
                            style="border: 1px solid #ced4da; border-radius: 6px; padding: 4px 8px; font-size: 0.85rem;">
                        <span style="color: #6c757d;">to</span>
                        <input type="date" name="end_date" value="{{ $end_date }}"
                            style="border: 1px solid #ced4da; border-radius: 6px; padding: 4px 8px; font-size: 0.85rem;">
                        <button type="submit"
                            style="background: #dc3545; color: #fff; border: none; border-radius: 6px;
                                   padding: 5px 14px; font-size: 0.85rem; cursor: pointer;">
                            Filter
                        </button>
                    </form>
                    <span style="background: #dc3545; color: #fff; padding: 6px 14px;
                                 border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                        Total: {{ number_format($totalAmount) }}
                    </span>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover table-sm" style="font-size: 0.9rem;">
                    <thead style="background: #e9ecef;">
                        <tr>
                            <th style="padding: 10px;">#</th>
                            <th style="padding: 10px;">Expense Type</th>
                            <th style="padding: 10px;">Amount</th>
                            <th style="padding: 10px;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $i => $expense)
                        <tr style="transition: background 0.15s;"
                            onmouseover="this.style.background='#fff5f5'"
                            onmouseout="this.style.background=''">
                            <td style="padding: 10px;">{{ $expenses->firstItem() + $i }}</td>
                            <td style="padding: 10px; font-weight: 500;">{{ $expense->expenseType->type ?? '-' }}</td>
                            <td style="padding: 10px; font-weight: 600; color: #dc3545;">
                                {{ number_format($expense->total_price) }}
                            </td>
                            <td style="padding: 10px; color: #6c757d;">{{ $expense->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 30px; color: #adb5bd;">
                                <i class="fa fa-inbox" style="font-size: 2rem; display: block; margin-bottom: 8px;"></i>
                                No expenses found for this period.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div style="margin-top: 15px;">{{ $expenses->appends(request()->query())->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection