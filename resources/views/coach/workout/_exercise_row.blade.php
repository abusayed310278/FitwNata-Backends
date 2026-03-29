@php
    // Set default values safely to avoid errors when rendering the JS template.
    // This checks if we are editing an existing exercise row.
    $isEditing = isset($selectedExercise);

    // Default values for the form fields
    $defaultExerciseId = $isEditing ? $selectedExercise->id : null;
    $defaultOrder = $isEditing ? $selectedExercise->pivot->order : (is_numeric($index) ? $index + 1 : '');
    $defaultSets = $isEditing ? $selectedExercise->pivot->sets : '';
    $defaultReps = $isEditing ? $selectedExercise->pivot->reps : '';
    $defaultDuration = $isEditing ? $selectedExercise->pivot->duration_seconds : '';
@endphp

<div class="row g-3 align-items-center mb-3 p-3 border rounded bg-light exercise-row">
    <div class="col-md-3">
        <label for="exercises_{{ $index }}_exercise_id" class="form-label">Exercise</label>
        <select name="exercises[{{ $index }}][exercise_id]" id="exercises_{{ $index }}_exercise_id" class="form-select" required>
            <option value="">-- Select Exercise --</option>
            @foreach($exercises as $exercise)
                <option value="{{ $exercise->id }}" @selected(old('exercises.'.$index.'.exercise_id', $defaultExerciseId) == $exercise->id)>
                    {{ $exercise->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label for="exercises_{{ $index }}_order" class="form-label">Order</label>
        <input type="number" name="exercises[{{ $index }}][order]" id="exercises_{{ $index }}_order" class="form-control" placeholder="e.g. 1" value="{{ old('exercises.'.$index.'.order', $defaultOrder) }}" min="1" required>
    </div>
    <div class="col-md-2">
        <label for="exercises_{{ $index }}_sets" class="form-label">Sets</label>
        <input type="number" name="exercises[{{ $index }}][sets]" id="exercises_{{ $index }}_sets" class="form-control" placeholder="e.g. 3" value="{{ old('exercises.'.$index.'.sets', $defaultSets) }}" min="1">
    </div>
    <div class="col-md-2">
        <label for="exercises_{{ $index }}_reps" class="form-label">Reps</label>
        <input type="number" name="exercises[{{ $index }}][reps]" id="exercises_{{ $index }}_reps" class="form-control" placeholder="e.g. 12" value="{{ old('exercises.'.$index.'.reps', $defaultReps) }}" min="1">
    </div>
    <div class="col-md-2">
        <label for="exercises_{{ $index }}_duration_seconds" class="form-label">Duration (sec)</label>
        <input type="number" name="exercises[{{ $index }}][duration_seconds]" id="exercises_{{ $index }}_duration_seconds" class="form-control" placeholder="e.g. 60" value="{{ old('exercises.'.$index.'.duration_seconds', $defaultDuration) }}" min="1">
    </div>
    <div class="col-md-1 d-flex align-items-end">
        <button type="button" class="btn btn-danger remove-exercise-btn">
            <i class="ti ti-trash"></i>
        </button>
    </div>
</div>
