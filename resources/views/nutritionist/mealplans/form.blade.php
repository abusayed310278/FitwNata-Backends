@push('styles')
<style>
    .section-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1rem;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.3rem;
    }
    .form-label { font-weight: 500; color: #495057; }
    .image-preview {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid #e9ecef;
    }
</style>
@endpush

@php
    $mealPlan = $mealPlan ?? new \App\Models\MealPlan();
@endphp

<div class="card mb-4">
    <div class="card-body">

        {{-- Basic Details --}}
        <h5 class="section-title">Basic Details</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control"
                       value="{{ old('title', $mealPlan->title ?? '') }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Goal <span class="text-danger">*</span></label>
                <select name="goal" class="form-select" required>
                    @foreach(['general_health', 'weight_loss', 'muscle_gain'] as $goal)
                        <option value="{{ $goal }}" {{ old('goal', $mealPlan->goal ?? '') == $goal ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $goal)) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $mealPlan->description ?? '') }}</textarea>
        </div>

        {{-- 🔹 Details --}}
        <h5 class="section-title">Plan Details</h5>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Duration (Days) <span class="text-danger">*</span></label>
                <input type="number" name="duration_days" class="form-control"
                       value="{{ old('duration_days', $mealPlan->duration_days ?? 1) }}" min="1" max="30" required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Total Calories <span class="text-danger">*</span></label>
                <input type="number" name="total_calories" class="form-control"
                       value="{{ old('total_calories', $mealPlan->total_calories ?? '') }}" required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Difficulty <span class="text-danger">*</span></label>
                <select name="difficulty" class="form-select" required>
                    @foreach(['easy', 'medium', 'hard'] as $diff)
                        <option value="{{ $diff }}" {{ old('difficulty', $mealPlan->difficulty ?? '') == $diff ? 'selected' : '' }}>
                            {{ ucfirst($diff) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- 🔹 Image & Premium --}}
        <h5 class="section-title">Image & Options</h5>
        <div class="row align-items-center">
            <div class="col-md-4 mb-3">
                <label class="form-label">Meal Plan Image</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
                <small class="text-muted">Upload jpg/png (max 2MB)</small>
            </div>

            <div class="col-md-4 mb-3 text-center">
                <img id="preview" class="image-preview"
                     src="{{ !empty($mealPlan->image_url) ? $mealPlan->image_url : asset('assets/images/default-meal.jpg') }}"
                     alt="Meal Plan Image">
            </div>

            <div class="col-md-4 mb-3">
                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" name="is_premium" value="1"
                           {{ old('is_premium', $mealPlan->is_premium ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label">Mark as Premium Meal Plan</label>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.getElementById('image')?.addEventListener('change', e => {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = evt => document.getElementById('preview').src = evt.target.result;
        reader.readAsDataURL(file);
    }
});
</script>
@endpush