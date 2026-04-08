@extends('layouts.vms')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>  
                    <li class="breadcrumb-item">Vehicles Managment</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
               <form class="dashform" action="{{ url('f-vehicles-dash') }}" method="POST" id="stockform">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <button type="button" class="btn btn-default btn-sm w-auto" id="reportrange"
                        style="white-space: nowrap;">
                        <i class="fa fa-calendar"></i>
                        <span id="reportrange-label" class="mx-1"></span>
                        <i class="fa fa-caret-down"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="card">
            <div class="card-body">
               
                <div class="container-fluid">

                    <!-- 🔹 Top Cards -->
                    <div class="row mb-4">

                        <!-- Total Vehicles -->
                        <div class="col-md-3">
                            <a href="{{ url('vms/total-vehicles') }}" style="text-decoration: none; color: inherit;">
                                <div class="card shadow-sm p-3"
                                    style="transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;"
                                    onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.12)'"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
                                    <h6 style="color: #6c757d; font-size: 0.85rem; margin-bottom: 6px;">
                                        <i class="fa fa-car me-1"></i> Total Vehicles
                                    </h6>
                                    <h3 style="margin: 0; font-weight: 700; color: #0d6efd;">{{ $totalVehicles }}</h3>
                                    <small style="color: #adb5bd;">Click to view all</small>
                                </div>
                            </a>
                        </div>

                        <!-- Active Trips -->
                        <div class="col-md-3">
                            <a href="{{ url('vms/active-trips') }}" style="text-decoration: none; color: inherit;">
                                <div class="card shadow-sm p-3"
                                    style="transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;"
                                    onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.12)'"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
                                    <h6 style="color: #6c757d; font-size: 0.85rem; margin-bottom: 6px;">
                                        <i class="fa fa-road me-1"></i> Active Trips
                                    </h6>
                                    <h3 style="margin: 0; font-weight: 700; color: #ffc107;">{{ $activeTrips }}</h3>
                                    <small style="color: #adb5bd;">Click to view all</small>
                                </div>
                            </a>
                        </div>

                        <!-- Total Expenses -->
                        <div class="col-md-3">
                            <a href="{{ url('vms/total-expenses') }}" style="text-decoration: none; color: inherit;">
                                <div class="card shadow-sm p-3"
                                    style="transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;"
                                    onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.12)'"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
                                    <h6 style="color: #6c757d; font-size: 0.85rem; margin-bottom: 6px;">
                                        <i class="fa fa-money me-1"></i> Total Expenses
                                    </h6>
                                    <h3 style="margin: 0; font-weight: 700; color: #dc3545;">{{ number_format($monthlyExpenses) }}</h3>
                                    <small style="color: #adb5bd;">Click to view all</small>
                                </div>
                            </a>
                        </div>

                        <!-- Pending Requests -->
                        <div class="col-md-3">
                            <a href="{{ url('vms/pending-requests') }}" style="text-decoration: none; color: inherit;">
                                <div class="card shadow-sm p-3"
                                    style="transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;"
                                    onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.12)'"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
                                    <h6 style="color: #6c757d; font-size: 0.85rem; margin-bottom: 6px;">
                                        <i class="fa fa-clock-o me-1"></i> Pending Requests
                                    </h6>
                                    <h3 style="margin: 0; font-weight: 700; color: #fd7e14;">{{ $pendingRequests }}</h3>
                                    <small style="color: #adb5bd;">Click to view all</small>
                                </div>
                            </a>
                        </div>

                    </div>

                    <!--  Charts -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card shadow-sm p-3">
                                <h6>Expenses Overview</h6>
                                <canvas id="expensesChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm p-3">
                                <h6>Trip Status</h6>
                                <canvas id="tripChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>

                    <!--  Recent Trips -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h6>Recent Trip Logs</h6>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Trip No</th>
                                        <th>Vehicle</th>
                                        <th>Driver</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTrips as $trip)
                                        <tr>
                                            <td>{{ $trip->trip_no }}</td>
                                            <td>{{ $trip->vehicleRequisition->vehicle->vehicle_name ?? '-' }}</td>
                                            <td>{{ $trip->vehicleRequisition->driver->full_name ?? '-' }}</td>
                                            <td>
                                                @if($trip->end_time)
                                                    <span class="badge bg-success">Completed</span>
                                                @else
                                                    <span class="badge bg-warning">Ongoing</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!--  Bottom Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <h6>Recent Expenses</h6>
                                </div>
                                <div class="card-body table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Amount</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentExpenses as $expense)
                                                <tr>
                                                    <td>{{ $expense->expenseType->type }}</td>
                                                    <td>{{ number_format($expense->total_price) }}</td>
                                                    <td>{{ $expense->created_at->format('Y-m-d') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <h6>Maintenance Alerts</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        @foreach($maintenanceAlerts as $alert)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ $alert->vehicle->vehicle_name ?? 'Vehicle' }}
                                                <span class="badge bg-danger">Due</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const expensesEl = document.getElementById('expensesChart');
    if (expensesEl) {
        new Chart(expensesEl, {
            type: 'bar',
            data: {
                labels: {!! json_encode($months ?? []) !!},
                datasets: [{
                    label: 'Expenses',
                    data: {!! json_encode($expensesData ?? []) !!},
                    backgroundColor:  'rgba(25, 135, 84, 0.6)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    const tripEl = document.getElementById('tripChart');
    if (tripEl) {
        new Chart(tripEl, {
            type: 'pie',
            data: {
                labels: ['Completed', 'Ongoing'],
                datasets: [{
                    data: [{{ $completedTrips ?? 0 }}, {{ $activeTrips ?? 0 }}],
                    backgroundColor: ['#198754', '#ffc107'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

});
</script>
@endsection