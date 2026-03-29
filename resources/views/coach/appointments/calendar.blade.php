@extends('layouts.adminApp')

@section('title', 'Appointments Management')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
    <style>
        .fc-col-header-cell-cushion {
            color: #006C6E !important;
        }

        .fc-button {
            color: black !important;
        }

        .fc-today-button {
            color: white !important;
        }

        /* Calendar button styling */
        .fc .fc-button {
            background-color: #006C6E;
            /* bootstrap primary */
            border: none;
            color: white;
        }

        .fc .fc-button:hover {
            background-color: #006C6E;
            color: #fff !important;
        }

        .fc .fc-button-active {
            background-color: #006C6E !important;
            color: #fff !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Appointments Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">My Appointments</h6>

            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="eventDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Appointment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Title:</strong> <span id="eventTitle"></span></p>
                    <p><strong>Start:</strong> <span id="eventStart"></span></p>
                    <p><strong>End:</strong> <span id="eventEnd"></span></p>
                    <p><strong>Status:</strong> <span id="eventStatus"></span></p>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth', // monthly view
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: '{{ route('coach.appointments.events') }}',
                eventDisplay: 'block', // 👈 ensures colored blocks instead of just dots
                eventClick: function(info) {
                    info.jsEvent.preventDefault();

                    document.getElementById('eventTitle').innerText = info.event.title;
                    document.getElementById('eventStart').innerText = info.event.start.toLocaleString();
                    document.getElementById('eventEnd').innerText = info.event.end ? info.event.end
                        .toLocaleString() : 'N/A';

                    // show status with Bootstrap badge
                    let status = info.event.extendedProps.status || 'N/A';
                    let statusColors = {
                        pending: 'warning',
                        approved: 'info',
                        completed: 'success',
                        canceled: 'danger'
                    };
                    let badgeClass = statusColors[status] ? `badge bg-${statusColors[status]}` :
                        'badge bg-secondary';
                    document.getElementById('eventStatus').innerHTML =
                        `<span class="${badgeClass}">${status}</span>`;

                    var modal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
                    modal.show();
                }
            });

            calendar.render();
        });
    </script>
@endpush
