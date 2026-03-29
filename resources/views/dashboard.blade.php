@extends('layouts.adminApp')

@section('title', 'Dashboard')

@push('styles')

<style>
    #search-results {
    background-color: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 6px;
}

#search-results .list-group-item {
    border: none;
    padding: 10px 12px;
    transition: background-color 0.2s;
}

#search-results .list-group-item:hover,
#search-results .list-group-item.active {
    background-color: #006C6E;
    color: white;
}
</style>
@endpush

@section('content')
    {{-- Header is included at the top of the content --}}
    @include('layouts.partials.header')

    {{-- The rest of the dashboard content --}}
    <div class="container-fluid p-0">
        <h2 class="h3 mb-4 text-gray-800 font-weight-bold">Dashboard Overview</h2>

        <!-- Top Cards Row -->
        <div class="row mb-4">
            <!-- Total Users Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow-sm h-100 dashboard-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="icon-circle"><i class="fas fa-users"></i></div>
                            {{-- <div class="percentage-badge">+12.5%</div> --}}
                        </div>
                        <div class="h2 mb-1 font-weight-bold text-white">{{ $totalUsers }}</div>
                        <div class="text-sm text-white-75">Total Users</div>
                    </div>
                </div>
            </div>
            <!-- Active Subscriptions Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow-sm h-100 dashboard-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="icon-circle"><i class="fas fa-check-circle"></i></div>
                            {{-- <div class="percentage-badge">+8.3%</div> --}}
                        </div>
                        <div class="h2 mb-1 font-weight-bold text-white">{{ $totalActiveSubscriptions }}</div>
                        <div class="text-sm text-white-75">Active Subscriptions</div>
                    </div>
                </div>
            </div>
            <!-- Revenue (Monthly) Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow-sm h-100 dashboard-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="icon-circle"><i class="fas fa-dollar-sign"></i></div>
                            {{-- <div class="percentage-badge">+15.2%</div> --}}
                        </div>
                        <div class="h2 mb-1 font-weight-bold text-white">{{ $revenueThisMonth }}</div>
                        <div class="text-sm text-white-75">Order Revenue <br>(Current Month)</div>
                    </div>
                </div>
            </div>
            <!-- Pending Appointments Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow-sm h-100 dashboard-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="icon-circle"><i class="fas fa-calendar-alt"></i></div>
                            {{-- <div class="urgent-badge">43 urgent</div> --}}
                        </div>
                        <div class="h2 mb-1 font-weight-bold text-white">{{ $pendingAppointments }}</div>
                        <div class="text-sm text-white-75">Pending Appointments</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-xl-8 col-lg-7 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-gray-800">Monthly Active Users (Last 12 Months)</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area-dashboard"><canvas id="monthlyActiveUsersChart"></canvas></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-gray-800">Revenue Overview</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-bar-dashboard"><canvas id="revenueBarChart"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lists Row -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-gray-800">Recent Activities</h6>
                    </div>
                    <div class="card-body">
                        @if ($recentActivities->count() > 0)
                            @foreach ($recentActivities as $item)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="{{ $item['icon'] }}" 
                                            alt="User Avatar" 
                                            class="rounded-circle me-3"
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark">{{ $item['message'] }}</div>
                                        <div class="small text-muted">{{ $item['name'] }}</div>
                                        <div class="small text-muted">{{ $item['time'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-bell fa-2x mb-2 text-secondary"></i>
                                <div>No recent activities yet 📭</div>
                                <small class="d-block mt-1">New subscriptions or orders will appear here.</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-gray-800">System Alerts</h6>
                    </div>
                    <div class="card-body">
                        @if ($systemAlerts->count() > 0)
                            @foreach ($systemAlerts as $alert)
                                <div class="alert-item">
                                    <div>
                                        <div class="font-weight-bold text-dark">
                                            Order #{{ $alert->order_number }} is pending
                                        </div>
                                        <small class="text-muted">
                                            Placed {{ $alert->order_date ? $alert->order_date->diffForHumans() : $alert->created_at->diffForHumans() }}
                                            — Total: ${{ number_format($alert->total_amount, 2) }}
                                        </small>
                                    </div>
                                    <a href="{{ route('order.show', $alert->id) }}" class="alert-view-btn">View</a>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                                <div>All systems normal ✅</div>
                                <small class="d-block mt-1">No pending orders or alerts at the moment.</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Search Bar (Visible on smaller screens) -->
    <div class="d-lg-none mb-4">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Search" aria-label="search">
            <div class="input-group-prepend hover-cursor">
                <span class="input-group-text">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart.js scripts to render the graphs
        const monthlyActiveUsersLabels = @json($monthlyActiveUsersLabels);
        const monthlyActiveUsersData = @json($monthlyActiveUsersData);
        const revenueBarLabels = @json($revenueLabels);
        const revenueBarData = @json($revenueData);

        const mauCtx = document.getElementById('monthlyActiveUsersChart').getContext('2d');
        new Chart(mauCtx, {
            type: 'line',
            data: {
                labels: monthlyActiveUsersLabels,
                datasets: [{
                    label: 'Active Customers',
                    data: monthlyActiveUsersData,
                    borderColor: '#006C6E',
                    backgroundColor: 'rgba(0, 108, 110, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#006C6E'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 10,
                            color: '#6B7280'
                        },
                        grid: {
                            borderDash: [5, 5],
                            color: '#E5E7EB'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: (context) => `${context.parsed.y} active users`
                        }
                    }
                }
            }
        });

        const revBarCtx = document.getElementById('revenueBarChart').getContext('2d');
        new Chart(revBarCtx, {
            type: 'bar',
            data: {
                labels: revenueBarLabels,
                datasets: [{
                    data: revenueBarData,
                    backgroundColor: ['#A0DED6', '#A0DED6', '#A0DED6', '#006C6E', '#A0DED6', '#006C6E'],
                    borderRadius: 6,
                    barPercentage: 0.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        grid: {
                            display: true
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('dashboard-search');
            const resultsList = document.getElementById('search-results');
            if (!searchInput || !resultsList) return;

            // Gather all sidebar menu links
            const menuLinks = Array.from(document.querySelectorAll('#sidebar a.nav-link'))
                .map(link => ({
                    name: link.querySelector('.menu-title')?.textContent.trim() || '',
                    href: link.getAttribute('href') || '#'
                }))
                .filter(item => item.name);

            // Typing listener
            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                resultsList.innerHTML = '';

                if (!query) {
                    resultsList.style.display = 'none';
                    return;
                }

                // Filter sidebar items
                const matches = menuLinks.filter(item =>
                    item.name.toLowerCase().includes(query)
                );

                if (matches.length === 0) {
                    resultsList.innerHTML = `<li class="list-group-item text-muted small text-center">No matches found</li>`;
                } else {
                    matches.forEach(item => {
                        const li = document.createElement('li');
                        li.classList.add('list-group-item', 'list-group-item-action');
                        li.style.cursor = 'pointer';
                        li.textContent = item.name;
                        li.addEventListener('click', () => {
                            window.location.href = item.href;
                        });
                        resultsList.appendChild(li);
                    });
                }

                resultsList.style.display = 'block';
            });

            // Hide when clicking outside
            document.addEventListener('click', e => {
                if (!resultsList.contains(e.target) && e.target !== searchInput) {
                    resultsList.style.display = 'none';
                }
            });

            // Optional: keyboard navigation (↑ ↓ Enter)
            let selectedIndex = -1;
            searchInput.addEventListener('keydown', function (e) {
                const items = resultsList.querySelectorAll('.list-group-item');
                if (!items.length) return;

                if (e.key === 'ArrowDown') {
                    selectedIndex = (selectedIndex + 1) % items.length;
                    updateSelection(items);
                } else if (e.key === 'ArrowUp') {
                    selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                    updateSelection(items);
                } else if (e.key === 'Enter' && selectedIndex >= 0) {
                    items[selectedIndex].click();
                }
            });

            function updateSelection(items) {
                items.forEach((el, i) => {
                    el.classList.toggle('active', i === selectedIndex);
                });
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        /* Dashboard Specific Styles */
        .card {
            border: 1px solid #EAECEF;
            border-radius: 12px;
            box-shadow: none;
        }

        .card-header {
            border-bottom: 1px solid #EAECEF;
            background-color: white;
        }

        .text-white-75 {
            color: rgba(255, 255, 255, 0.75) !important;
        }

        /* Top Cards */
        .dashboard-card {
            background: linear-gradient(95deg, #20C3B5 0%, #17A598 100%);
            color: white;
            border-radius: 12px;
            border: none;
        }

        .dashboard-card .card-body {
            padding: 1.25rem;
        }

        .dashboard-card .icon-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(255, 255, 255, 0.15);
            font-size: 1rem;
        }

        .dashboard-card .percentage-badge {
            background-color: rgba(255, 255, 255, 0.15);
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .dashboard-card .urgent-badge {
            background-color: #FBBF24;
            color: #78350F;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Charts & Lists */
        .chart-area-dashboard,
        .chart-bar-dashboard {
            height: 320px;
        }

        .alert-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #F3F4F6;
        }

        .alert-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .alert-item:first-child {
            padding-top: 0;
        }

        .alert-view-btn {
            color: #3B82F6;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Mobile Search Bar */
        @media (max-width: 991px) {
            .d-lg-none.mb-4 {
                margin-top: 1rem;
            }
        }
    </style>
@endpush
