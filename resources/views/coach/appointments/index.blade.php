@extends('layouts.adminApp')

@section('title', 'Appointments Management')

@push('styles')
    <!-- DataTables CSS -->
    @include('components.datatable-styles')
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Appointments Management</h1>
                <p class="mb-0">Manage your coaching sessions and client appointments</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('coach.appointments.calendar') }}" class="btn btn-success">
                    <i class="fas fa-calendar me-2"></i>Calendar View
                </a>
                <a href="{{ route('coach.availability.index') }}" class="btn btn-info">
                    <i class="fas fa-clock me-2"></i>Set Availability
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Appointments
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Approval
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Today's Sessions</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['today'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Upcoming</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['upcoming'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
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

        <!-- Appointments Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">My Appointments</h6>
                {{-- <div class="d-flex align-items-center">
                <select id="status-filter" class="form-select form-select-sm me-2">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="completed">Completed</option>
                    <option value="canceled">Canceled</option>
                    <option value="rescheduled">Rescheduled</option>
                </select>
                <select id="type-filter" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="workout">Workout</option>
                    <option value="nutrition">Nutrition</option>
                    <option value="consultation">Consultation</option>
                    <option value="follow_up">Follow-up</option>
                </select>
            </div> --}}
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="appointments-table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Type</th>
                                <th>Scheduled Date</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Reschedule Modal -->
    <div class="modal fade" id="rescheduleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reschedule Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="rescheduleForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="new_datetime" class="form-label">New Date & Time</label>
                            <input type="datetime-local" class="form-control" id="new_datetime" name="new_datetime"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="reschedule_reason" class="form-label">Reason for Reschedule</label>
                            <textarea class="form-control" id="reschedule_reason" name="reschedule_reason" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Reschedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="cancelForm">
                    <div class="modal-body">
                        <p>Are you sure you want to cancel this appointment?</p>
                        <div class="mb-3">
                            <label for="cancel_reason" class="form-label">Reason for Cancellation</label>
                            <textarea class="form-control" id="cancel_reason" name="cancel_reason" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Cancel Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Complete Modal -->
    <div class="modal fade" id="completeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Complete Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="completeForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="session_notes" class="form-label">Session Notes</label>
                            <textarea class="form-control" id="session_notes" name="session_notes" rows="4"
                                placeholder="Add your notes about this session..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="client_feedback" class="form-label">Client Feedback (Optional)</label>
                            <textarea class="form-control" id="client_feedback" name="client_feedback" rows="3"
                                placeholder="Any feedback from the client..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Mark as Complete</button>
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
            var table = $('#appointments-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('coach.appointments.data') }}",
                    data: function(d) {
                        d.status = $('#status-filter').val();
                        d.type = $('#type-filter').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'client',
                        name: 'client'
                    },
                    {
                        data: 'appointment_type',
                        name: 'appointment_type',
                        searchable: false
                    },
                    {
                        data: 'scheduled_at',
                        name: 'scheduled_at',
                        searchable: false
                    },
                    {
                        data: 'duration',
                        name: 'duration',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status'
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
            $('#status-filter, #type-filter').on('change', function() {
                table.draw();
            });
        });

        let currentAppointmentId = null;

        function approveAppointment(appointmentId) {
            showConfirm("Are you sure?", "Do you want to approve this appointment?")
                .then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/coach/appointments/${appointmentId}/approve`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': getCsrfToken(),
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire("Approved!", data.message, "success")
                                        .then(() => {
                                            // Refresh DataTable
                                            $('#appointments-table').DataTable().ajax.reload();
                                        });
                                } else {
                                    Swal.fire("Error", data.message || "Something went wrong.", "error");
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire("Error", "An error occurred while approving the appointment.", "error");
                            });
                    }
                });
        }


        function rescheduleAppointment(appointmentId) {
            currentAppointmentId = appointmentId;
            $('#rescheduleModal').modal('show');
        }

        function cancelAppointment(appointmentId) {
            currentAppointmentId = appointmentId;
            $('#cancelModal').modal('show');
        }

        function completeAppointment(appointmentId) {
            currentAppointmentId = appointmentId;
            $('#completeModal').modal('show');
        }

        $('#rescheduleForm').on('submit', function(e) {
            e.preventDefault();

            $.post(`/coach/appointments/${currentAppointmentId}/reschedule`, {
                    new_datetime: $('#new_datetime').val(),
                    reschedule_reason: $('#reschedule_reason').val(),
                    _token: '{{ csrf_token() }}'
                })
                .done(function(response) {
                    if (response.success) {
                        $('#rescheduleModal').modal('hide');
                        $('#appointments-table').DataTable().ajax.reload();

                        Swal.fire({
                            icon: 'success',
                            title: 'Rescheduled!',
                            text: response.message,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .fail(function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errorMessages = Object.values(xhr.responseJSON.errors)
                            .flat()
                            .join('<br>');

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: errorMessages,
                            confirmButtonColor: '#d33',
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong while rescheduling.',
                            confirmButtonColor: '#d33',
                        });
                    }
                });
        });


        // Handle cancel form submission
        $('#cancelForm').on('submit', function(e) {
            e.preventDefault();

            $.post(`/coach/appointments/${currentAppointmentId}/cancel`, {
                    cancel_reason: $('#cancel_reason').val(),
                    _token: '{{ csrf_token() }}'
                })
                .done(function(response) {
                    if (response.success) {
                        $('#cancelModal').modal('hide');
                        $('#appointments-table').DataTable().ajax.reload();

                        Swal.fire({
                            icon: 'success',
                            title: 'Canceled!',
                            text: response.message,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .fail(function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errorMessages = Object.values(xhr.responseJSON.errors)
                            .flat()
                            .join('<br>');

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: errorMessages,
                            confirmButtonColor: '#d33',
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong while canceling appointment.',
                            confirmButtonColor: '#d33',
                        });
                    }
                });
        });


        // Handle complete form submission
        $('#completeForm').on('submit', function(e) {
            e.preventDefault();

            $.post(`/coach/appointments/${currentAppointmentId}/complete`, {
                    session_notes: $('#session_notes').val(),
                    client_feedback: $('#client_feedback').val(),
                    _token: '{{ csrf_token() }}'
                })
                .done(function(response) {
                    if (response.success) {
                        $('#completeModal').modal('hide');
                        $('#appointments-table').DataTable().ajax.reload();

                        Swal.fire({
                            icon: 'success',
                            title: 'Completed!',
                            text: response.message,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .fail(function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errorMessages = Object.values(xhr.responseJSON.errors)
                            .flat()
                            .join('<br>');

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: errorMessages,
                            confirmButtonColor: '#d33',
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong while completing appointment.',
                            confirmButtonColor: '#d33',
                        });
                    }
                });
        });
    </script>
@endpush
