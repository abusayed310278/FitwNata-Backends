@extends('layouts.adminApp')

@section('title', 'Clients Management')
@push('styles')
    <!-- DataTables CSS -->
    @include('components.datatable-styles')
@endpush
@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Clients Management</h1>
                <p class="mb-0">View and manage your clients' progress and appointments</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('coach.appointments.index') }}" class="btn btn-info">
                    <i class="fas fa-calendar-alt me-2"></i>View Appointments
                </a>
                <button class="btn btn-success" onclick="showScheduleModal()">
                    <i class="fas fa-calendar-plus me-2"></i>Schedule Follow-up
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Clients</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_clients'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Clients</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_clients'] }}</div>
                                <div class="text-xs text-muted">Last month activity</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">New This Month</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['new_this_month'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Clients Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">My Clients</h6>
                <div class="d-flex align-items-center">
                    {{-- <div class="input-group input-group-sm me-2">
                    <input type="text" class="form-control" id="search-input" placeholder="Search clients...">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
                <select id="activity-filter" class="form-select form-select-sm">
                    <option value="">All Clients</option>
                    <option value="active">Active (Recent Activity)</option>
                    <option value="inactive">Inactive (No Recent Activity)</option>
                </select> --}}
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="clients-table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Total Sessions</th>
                                <th>Last Session</th>
                                <th>Next Appointment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Note Modal -->
    <div class="modal fade" id="addNoteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Client Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addNoteForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="note_type" class="form-label">Note Type</label>
                            <select class="form-select" id="note_type" name="note_type" required>
                                <option value="performance">Performance</option>
                                <option value="feedback">Feedback</option>
                                <option value="general">General</option>
                                <option value="goal">Goal Setting</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="note" class="form-label">Note</label>
                            <textarea class="form-control" id="note" name="note" rows="4"
                                placeholder="Add your note about this client..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Schedule Follow-up Modal -->
    <div class="modal fade" id="scheduleFollowupModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Schedule Follow-up Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="scheduleFollowupForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="appointment_type" class="form-label">Session Type</label>
                            <select class="form-select" id="appointment_type" name="appointment_type" required>
                                <option value="follow_up">Follow-up Session</option>
                                <option value="workout">Workout Session</option>
                                <option value="nutrition">Nutrition Consultation</option>
                                <option value="consultation">General Consultation</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="scheduled_at" class="form-label">Date & Time</label>
                            <input type="datetime-local" class="form-control" id="scheduled_at" name="scheduled_at"
                                min="{{ now()->addHour()->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                            <select class="form-select" id="duration_minutes" name="duration_minutes" required>
                                <option value="30">30 minutes</option>
                                <option value="45">45 minutes</option>
                                <option value="60" selected>60 minutes</option>
                                <option value="90">90 minutes</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="followup_notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="followup_notes" name="notes" rows="3"
                                placeholder="Additional notes for this appointment..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Schedule Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('components.datatable-scripts')
    <script>
        $(document).ready(function() {
            var table = $('#clients-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('coach.clients.data') }}",
                    data: function(d) {
                        d.activity = $('#activity-filter').val();
                        d.search = $('#search-input').val();
                    }
                },
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'total_sessions',
                        name: 'total_sessions',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'last_session',
                        name: 'last_session',
                        searchable: false
                    },
                    {
                        data: 'next_appointment',
                        name: 'next_appointment',
                        searchable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [3, 'desc']
                ]
            });

            // Filter functionality
            $('#activity-filter').on('change', function() {
                table.draw();
            });

            // Search functionality
            $('#search-input').on('keyup', function() {
                table.draw();
            });
        });

        let currentClientId = null;

        function addNote(clientId, clientName) {
            currentClientId = clientId;
            $('#addNoteModal .modal-title').text(`Add Note for ${clientName}`);
            $('#addNoteModal').modal('show');
        }

        function scheduleFollowup(clientId, clientName) {
            currentClientId = clientId;
            $('#scheduleFollowupModal .modal-title').text(`Schedule Follow-up for ${clientName}`);
            $('#scheduleFollowupModal').modal('show');
        }

        function showScheduleModal() {
            // This would show a client selection modal for scheduling
            alert('Please select a client from the table to schedule a follow-up appointment.');
        }

        // Handle add note form submission
        $('#addNoteForm').on('submit', function(e) {
            e.preventDefault();

            if (!currentClientId) {
                alert('No client selected');
                return;
            }

            $.post(`/coach/clients/${currentClientId}/notes`, {
                    note: $('#note').val(),
                    note_type: $('#note_type').val(),
                    _token: '{{ csrf_token() }}'
                })
                .done(function(response) {
                    if (response.success) {
                        $('#addNoteModal').modal('hide');
                        alert(response.message);
                        // Reset form
                        $('#addNoteForm')[0].reset();
                    } else {
                        alert(response.message);
                    }
                })
                .fail(function() {
                    alert('Error adding note');
                });
        });

        // Handle schedule follow-up form submission
        $('#scheduleFollowupForm').on('submit', function(e) {
            e.preventDefault();

            if (!currentClientId) {
                alert('No client selected');
                return;
            }

            $.post(`/coach/clients/${currentClientId}/schedule-followup`, {
                    appointment_type: $('#appointment_type').val(),
                    scheduled_at: $('#scheduled_at').val(),
                    duration_minutes: $('#duration_minutes').val(),
                    notes: $('#followup_notes').val(),
                    _token: '{{ csrf_token() }}'
                })
                .done(function(response) {
                    if (response.success) {
                        $('#scheduleFollowupModal').modal('hide');
                        $('#clients-table').DataTable().ajax.reload();
                        alert(response.message);
                        // Reset form
                        $('#scheduleFollowupForm')[0].reset();
                    } else {
                        alert(response.message);
                    }
                })
                .fail(function() {
                    alert('Error scheduling follow-up');
                });
        });

        // Reset modals when closed
        $('#addNoteModal, #scheduleFollowupModal').on('hidden.bs.modal', function() {
            currentClientId = null;
            $(this).find('form')[0].reset();
        });
    </script>
@endpush
