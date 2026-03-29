@csrf
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<div class="row">
    {{-- Main Workout Details --}}
    <div class="col-md-6 mb-3">
        <label for="title" class="form-label">Workout Title</label>
        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
            value="{{ old('title', $workout->title ?? '') }}" required>
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="level" class="form-label">Difficulty Level</label>
        <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
            <option value="beginner" @selected(old('level', $workout->level ?? '') == 'beginner')>Beginner</option>
            <option value="intermediate" @selected(old('level', $workout->level ?? '') == 'intermediate')>Intermediate</option>
            <option value="advanced" @selected(old('level', $workout->level ?? '') == 'advanced')>Advanced</option>
        </select>
        @error('level')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="duration_minutes" class="form-label">Duration (minutes)</label>
        <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" id="duration_minutes"
            name="duration_minutes" value="{{ old('duration_minutes', $workout->duration ?? '') }}" required
            min="1">
        @error('duration_minutes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="calories_burned" class="form-label">Calories Burned (optional)</label>
        <input type="number" class="form-control @error('calories_burned') is-invalid @enderror" id="calories_burned"
            name="calories_burned" value="{{ old('calories_burned', $workout->calories_burned ?? '') }}" min="0">
        @error('calories_burned')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12 mb-3">
        <label for="thumbnail_url" class="form-label">Thumbnail URL (optional)</label>
        <input type="url" class="form-control @error('thumbnail_url') is-invalid @enderror" id="thumbnail_url"
            name="thumbnail_url" value="{{ old('thumbnail_url', $workout->thumbnail_url ?? '') }}">
        @error('thumbnail_url')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="plan_type" class="form-label">Prefference Plan Type</label>
        <select class="form-control @error('plan_type') is-invalid @enderror" id="plan_type" name="plan_type">
            <option value="">Select Plan Type</option>
            @foreach (planTypeOptions() as $plan)
                <option value="{{ $plan }}"
                    {{ old('plan_type', ($workout->is_premium ?? 0) == 1 ? 'premium' : 'free') == $plan ? 'selected' : '' }}>
                    {{ ucfirst($plan) }}
                </option>
            @endforeach
        </select>
        @error('plan_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="published_at" class="form-label">Publish Date *</label>
        <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" id="published_at"
            name="published_at"
            value="{{ old('published_at', @$workout->published_at ? @$workout->published_at : '') }}">
        @error('published_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        {{-- <small class="form-text text-muted">Leave empty to save as draft</small> --}}
    </div>
    <div class="col-md-6 mb-3">
        <label for="fitness_goals" class="form-label">Fitness Goals</label>
        <select class="form-control @error('fitness_goals') is-invalid @enderror" id="fitness_goals"
            name="fitness_goals">
            <option value="">Select Fitness Goal</option>
            @foreach (fitnessGoalsOptions() as $goal)
                <option value="{{ $goal }}"
                    {{ old('fitness_goals', $workout->fitness_goals ?? '') == $goal ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $goal)) }}
                </option>
            @endforeach
        </select>
        @error('fitness_goals')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="training_location" class="form-label">Training Location</label>
        <select class="form-control @error('training_location') is-invalid @enderror" id="training_location"
            name="training_location">
            <option value="">Select Training Location</option>
            @foreach (trainingLocationOptions() as $location)
                <option value="{{ $location }}"
                    {{ old('training_location', $workout->training_location ?? '') == $location ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $location)) }}
                </option>
            @endforeach
        </select>
        @error('training_location')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="health_conditions" class="form-label">Health Conditions</label>
        <select class="form-control @error('health_conditions') is-invalid @enderror" id="health_conditions"
            name="health_conditions">
            <option value="">Select Health Condition</option>
            @foreach (healthConditionsOptions() as $condition)
                <option value="{{ $condition }}"
                    {{ old('health_conditions', $workout->health_conditions ?? '') == $condition ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $condition)) }}
                </option>
            @endforeach
        </select>
        @error('health_conditions')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="gender_preference" class="form-label">Gender Preference</label>
        <select class="form-control @error('gender_preference') is-invalid @enderror" id="gender_preference"
            name="gender_preference">
            <option value="">Select Gender Preference</option>
            @foreach (genderOptions() as $gender)
                <option value="{{ $gender }}"
                    {{ old('gender_preference', $workout->gender_preference ?? '') == $gender ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $gender)) }}
                </option>
            @endforeach
        </select>
        @error('gender_preference')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="equipment_availability" class="form-label">Equipment Availability</label>
        <select class="form-control @error('equipment') is-invalid @enderror"
            id="equipment" name="equipment">
            <option value="">Select Equipment</option>
            @foreach (equipmentAvailabilityOptions() as $equipment)
                <option value="{{ $equipment }}"
                    {{ old('equipment', $workout->equipment ?? '') == $equipment ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $equipment)) }}
                </option>
            @endforeach
        </select>
        @error('equipment')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="type" class="form-label">Preferred Workout Types</label>
        <select class="form-control @error('type') is-invalid @enderror"
            id="type" name="type">
            <option value="">Select Workout Type</option>
            @foreach (preferredWorkoutTypesOptions() as $type)
                <option value="{{ $type }}"
                    {{ old('type', $workout->type ?? '') == $type ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                </option>
            @endforeach
        </select>
        @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>




    @include('coach.components.selectedTags')
    {{-- <div class="col-12 mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
            rows="3">{{ old('description', $workout->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div> --}}
    <div class="mb-3">
        <label class="form-label">Description</label>
        <div class="quill-editor" data-textarea="description" data-placeholder="Write your description here..."
            style="height: 200px;">
            {!! old('description', $workout->description ?? '') !!}
        </div>
        <textarea name="description" id="description" style="display: none;" required>{{ old('description', $workout->description ?? '') }}</textarea>
    </div>

    {{-- <div class="mb-3">
        <label class="form-label">Tips</label>
        <div class="quill-editor" data-textarea="tips" data-placeholder="Write your tips here..."
            style="height: 200px;">
            {!! old('tips', $workout->tips ?? '') !!}
        </div>
        <textarea name="tips" id="tips" style="display: none;" required>{{ old('tips', $workout->tips ?? '') }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Instructions</label>
        <div class="quill-editor" data-textarea="instructions" data-placeholder="Write your tips here..."
            style="height: 200px;">
            {!! old('tips', $workout->instructions ?? '') !!}
        </div>
        <textarea name="instructions" id="instructions" style="display: none;" required>{{ old('tips', $workout->instructions ?? '') }}</textarea>
    </div> --}}


</div>

<hr>

{{-- Exercises Repeater Section --}}
<h4>Exercises</h4>
<div id="exercises-container">
    {{-- Existing exercises for edit --}}
    @if (isset($workout) && $workout->exercises->count())
        @foreach ($workout->exercises as $index => $exercise)
            @include('coach.workout._exercise_row', ['index' => $index, 'selectedExercise' => $exercise])
        @endforeach
    @endif
</div>
<button type="button" class="btn btn-outline-success mt-2" id="add-exercise-btn">
    <i class="ti ti-plus"></i> Add Exercise
</button>

{{-- Hidden template --}}
<div id="exercise-template" style="display:none;">
    @include('coach.workout._exercise_row', ['index' => '__INDEX__'])
</div>

@push('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script>
        $(document).ready(function() {
            let exerciseIndex = {{ isset($workout) ? $workout->exercises->count() : 0 }};
            $('#exercise-template').find('input, select, textarea').prop('disabled', true);

            $('#add-exercise-btn').on('click', function() {
                let template = $('#exercise-template').html();

                let newRow = $(template.replace(/__INDEX__/g, exerciseIndex));

                newRow.find('input, select, textarea').prop('disabled', false);

                $('#exercises-container').append(newRow);

                exerciseIndex++;
            });

            $('#exercises-container').on('click', '.remove-exercise-btn', function() {
                $(this).closest('.exercise-row').remove();
            });
        });
    </script>
    <script>
        let quill;

        $(document).ready(function() {
            $('.quill-editor').each(function() {
                const $editorDiv = $(this);
                const textareaId = $editorDiv.data('textarea'); // e.g., 'description' or 'tips'
                const placeholder = $editorDiv.data('placeholder') || '';

                // Initialize Quill for this editor
                const quill = new Quill(this, {
                    theme: 'snow',
                    placeholder: placeholder,
                    modules: {
                        toolbar: [
                            [{
                                'header': [1, 2, 3, 4, 5, 6, false]
                            }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{
                                'color': []
                            }, {
                                'background': []
                            }],
                            [{
                                'list': 'ordered'
                            }, {
                                'list': 'bullet'
                            }],
                            [{
                                'indent': '-1'
                            }, {
                                'indent': '+1'
                            }],
                            ['blockquote', 'code-block'],
                            ['link', 'image'],
                            ['clean']
                        ]
                    }
                });

                // Update corresponding hidden textarea
                quill.on('text-change', function() {
                    $('#' + textareaId).val(quill.root.innerHTML);
                });

                // Optional: store quill instance in div for future use
                $editorDiv.data('quill', quill);
            });
        });
    </script>
@endpush
