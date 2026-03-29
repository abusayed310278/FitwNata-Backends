@extends('layouts.adminApp')

@section('title', 'Availability Settings')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Availability Settings</h1>
                <p class="mb-0">Set your available days and hours for client bookings</p>
            </div>
            <a href="{{ route('coach.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Weekly Availability -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-calendar-week me-2"></i>Weekly Availability Schedule
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="availability-form">
                            @csrf
                            <div class="row">
                                @foreach ($daysOfWeek as $dayIndex => $dayName)
                                    {{-- {{ dd($availabilities, $dayIndex,$availabilities->has('Sunday')) }} --}}

                                    <div class="col-12 mb-4">
                                        <div class="availability-day border rounded p-3">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input day-toggle" type="checkbox"
                                                        id="day_{{ $dayIndex }}" data-day="{{ $dayIndex }}"
                                                        {{ $availabilities->has($dayIndex) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="day_{{ $dayIndex }}">
                                                        {{ $dayName }}
                                                    </label>
                                                </div>
                                                @if ($availabilities->has($dayIndex) && $availabilities[$dayIndex]->count() > 0)
                                                    <span class="badge bg-success">
                                                        {{ $availabilities[$dayIndex]->count() }} slot(s) set
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="time-slots" id="slots_{{ $dayIndex }}"
                                                style="display: {{ $availabilities->has($dayIndex) ? 'block' : 'none' }}">
                                                @if ($availabilities->has($dayIndex))
                                                    @foreach ($availabilities[$dayIndex] as $index => $availability)
                                                        <div class="time-slot-row mb-2">
                                                            <div class="row align-items-center">
                                                                <div class="col-md-4">
                                                                    <input type="time" class="form-control"
                                                                        name="availability[{{ $dayIndex }}][{{ $index }}][start_time]"
                                                                        value="{{ $availability->start_time }}" required>
                                                                </div>
                                                                <div class="col-md-1 text-center">
                                                                    <span class="text-muted">to</span>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input type="time" class="form-control"
                                                                        name="availability[{{ $dayIndex }}][{{ $index }}][end_time]"
                                                                        value="{{ $availability->end_time }}" required>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-danger remove-slot">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-primary add-slot"
                                                                        data-day="{{ $dayIndex }}">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="time-slot-row mb-2">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-4">
                                                                <input type="time" class="form-control"
                                                                    name="availability[{{ $dayIndex }}][0][start_time]"
                                                                    value="09:00" required>
                                                            </div>
                                                            <div class="col-md-1 text-center">
                                                                <span class="text-muted">to</span>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="time" class="form-control"
                                                                    name="availability[{{ $dayIndex }}][0][end_time]"
                                                                    value="17:00" required>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-danger remove-slot">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-primary add-slot"
                                                                    data-day="{{ $dayIndex }}">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>Save Availability Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Blocked Times & Quick Actions -->
            <div class="col-lg-4">
                <!-- Block Specific Time -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-ban me-2"></i>Block Specific Time
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Block specific dates/times when you're not available</p>
                        <form id="block-time-form">
                            @csrf
                            <div class="mb-3">
                                <label for="blocked_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="blocked_date" name="blocked_date"
                                    min="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="start_time" class="form-label">Start Time</label>
                                        <input type="time" class="form-control" id="start_time" name="start_time"
                                            required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="end_time" class="form-label">End Time</label>
                                        <input type="time" class="form-control" id="end_time" name="end_time"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="reason" class="form-label">Reason (Optional)</label>
                                <input type="text" class="form-control" id="reason" name="reason"
                                    placeholder="e.g., Personal appointment, Vacation">
                            </div>
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-ban me-2"></i>Block Time
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Current Blocked Times -->
                @if ($blockedTimes->count() > 0)
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-list me-2"></i>Blocked Times
                            </h6>
                        </div>
                        <div class="card-body">
                            @foreach ($blockedTimes as $blocked)
                                <div class="blocked-time-item border-bottom pb-2 mb-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <strong>{{ $blocked->blocked_date->format('M d, Y') }}</strong><br>
                                            <small class="text-muted">
                                                {{ Carbon\Carbon::parse($blocked->start_time)->format('g:i A') }} -
                                                {{ Carbon\Carbon::parse($blocked->end_time)->format('g:i A') }}
                                            </small>
                                            @if ($blocked->block_reason)
                                                <br><small class="text-info">{{ $blocked->block_reason }}</small>
                                            @endif
                                        </div>
                                        <button class="btn btn-sm btn-outline-danger"
                                            onclick="unblockTime({{ $blocked->id }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Quick Presets -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-magic me-2"></i>Quick Presets
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary" onclick="applyPreset('business')">
                                Business Hours (9 AM - 5 PM, Mon-Fri)
                            </button>
                            <button class="btn btn-outline-primary" onclick="applyPreset('evening')">
                                Evening Hours (6 PM - 9 PM, Mon-Fri)
                            </button>
                            <button class="btn btn-outline-primary" onclick="applyPreset('weekend')">
                                Weekend Only (9 AM - 3 PM, Sat-Sun)
                            </button>
                            <button class="btn btn-outline-danger" onclick="clearAll()">
                                Clear All Availability
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script></script>

    <script>
        $(document).ready(function() {

            // Day toggle functionality
            $('.day-toggle').on('change', function() {
                const dayIndex = $(this).data('day');
                const slotsContainer = $(`#slots_${dayIndex}`);

                if ($(this).is(':checked')) {
                    slotsContainer.show();
                } else {
                    slotsContainer.hide();
                }
            });

            // Add time slot
            $(document).on('click', '.add-slot', function() {
                const dayIndex = $(this).data('day');
                const slotsContainer = $(`#slots_${dayIndex}`);
                const slotCount = slotsContainer.find('.time-slot-row').length;

                const newSlot = `
            <div class="time-slot-row mb-2">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <input type="time" class="form-control"
                               name="availability[${dayIndex}][${slotCount}][start_time]"
                               value="09:00" required>
                    </div>
                    <div class="col-md-1 text-center">
                        <span class="text-muted">to</span>
                    </div>
                    <div class="col-md-4">
                        <input type="time" class="form-control"
                               name="availability[${dayIndex}][${slotCount}][end_time]"
                               value="17:00" required>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-slot">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary add-slot" data-day="${dayIndex}">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>`;

                slotsContainer.append(newSlot);
            });

            // Remove time slot
            $(document).on('click', '.remove-slot', function() {
                const slotRow = $(this).closest('.time-slot-row');
                const slotsContainer = slotRow.closest('.time-slots');

                if (slotsContainer.find('.time-slot-row').length > 1) {
                    slotRow.remove();
                } else {
                    alert('You must have at least one time slot for each available day.');
                }
            });
        });

        // Save availability settings
        $('#availability-form').on('submit', function(e) {
            e.preventDefault();

            const availability = [];

            // Process checked days
            $('.day-toggle:checked').each(function() {
                const dayIndex = $(this).data('day');
                const daySlots = $(`#slots_${dayIndex} .time-slot-row`);

                daySlots.each(function() {
                    const startTime = $(this).find('input[name$="[start_time]"]').val();
                    const endTime   = $(this).find('input[name$="[end_time]"]').val();

                    if (startTime && endTime) {
                        availability.push({
                            day: DAY_NAMES[dayIndex],   // <-- send "Monday", "Tuesday", ...
                            enabled: 'true',            // send as string; backend coerces
                            start_time: startTime,
                            end_time: endTime
                        });
                    }
                });
            });

            $.post("{{ route('coach.availability.update') }}", {
                    availability: availability,
                    _token: '{{ csrf_token() }}'
                })
                .done(function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: response.message,
                            confirmButtonColor: '#3085d6',
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Error updating availability',
                            confirmButtonColor: '#d33',
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
                            text: 'Something went wrong while saving availability.',
                            confirmButtonColor: '#d33',
                        });
                    }
                });
        });


        // Block specific time
        // Handle block time form submission
        $('#block-time-form').on('submit', function(e) {
            e.preventDefault();

            $.post("{{ route('coach.availability.block') }}", $(this).serialize())
                .done(function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Blocked!',
                            text: response.message,
                            confirmButtonColor: '#3085d6',
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonColor: '#d33',
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
                            text: 'Something went wrong while blocking time.',
                            confirmButtonColor: '#d33',
                        });
                    }
                });
        });


        // Unblock time
        function unblockTime(blockId) {
            if (confirm('Are you sure you want to unblock this time?')) {
                $.ajax({
                        url: `/coach/availability/unblock/${blockId}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        }
                    })
                    .done(function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    })
                    .fail(function() {
                        alert('Error unblocking time');
                    });
            }
        }

        // Apply presets
        function applyPreset(type) {
            Swal.fire({
                title: "Are you sure?",
                text: "This will replace your current availability settings.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, continue"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Clear current settings
                    $('.day-toggle').prop('checked', false);
                    $('.time-slots').hide();

                    switch (type) {
                        case 'business':
                            // Monday to Friday, 9 AM - 5 PM
                            for (let day = 1; day <= 5; day++) {
                                $(`#day_${day}`).prop('checked', true);
                                $(`#slots_${day}`).show();
                                $(`#slots_${day} input[name$="[start_time]"]`).first().val('09:00');
                                $(`#slots_${day} input[name$="[end_time]"]`).first().val('17:00');
                            }
                            break;
                        case 'evening':
                            // Monday to Friday, 6 PM - 9 PM
                            for (let day = 1; day <= 5; day++) {
                                $(`#day_${day}`).prop('checked', true);
                                $(`#slots_${day}`).show();
                                $(`#slots_${day} input[name$="[start_time]"]`).first().val('18:00');
                                $(`#slots_${day} input[name$="[end_time]"]`).first().val('21:00');
                            }
                            break;
                        case 'weekend':
                            // Saturday and Sunday, 9 AM - 3 PM
                            [0, 6].forEach(day => {
                                $(`#day_${day}`).prop('checked', true);
                                $(`#slots_${day}`).show();
                                $(`#slots_${day} input[name$="[start_time]"]`).first().val('09:00');
                                $(`#slots_${day} input[name$="[end_time]"]`).first().val('15:00');
                            });
                            break;
                    }

                    Swal.fire({
                        icon: "success",
                        title: "Applied!",
                        text: "Preset availability settings have been applied.",
                        confirmButtonColor: "#3085d6"
                    });
                }
            });
        }

        // Clear all availability
        function clearAll() {
            Swal.fire({
                title: "Are you sure?",
                text: "This will clear all availability settings.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, clear all"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('.day-toggle').prop('checked', false);
                    $('.time-slots').hide();

                    Swal.fire({
                        icon: "success",
                        title: "Cleared!",
                        text: "All availability settings have been cleared.",
                        confirmButtonColor: "#3085d6"
                    });
                }
            });
        }
    </script>

    <script>
        // If $daysOfWeek is like [0 => 'Sunday', 1 => 'Monday', ...]
        const DAY_NAMES = @json($daysOfWeek);
    </script>
@endpush
