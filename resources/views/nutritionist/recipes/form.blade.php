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
    $recipe = $recipe ?? new \App\Models\Recipe();
@endphp

<div class="card mb-4">
    <div class="card-body">

        {{-- 🔹 Basic Details --}}
        <h5 class="section-title">Basic Details</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $recipe->title ?? '') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Difficulty <span class="text-danger">*</span></label>
                <select name="difficulty" class="form-select" required>
                    @foreach(['easy', 'medium', 'hard'] as $diff)
                        <option value="{{ $diff }}" {{ old('difficulty', $recipe->difficulty ?? '') == $diff ? 'selected' : '' }}>
                            {{ ucfirst($diff) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $recipe->description ?? '') }}</textarea>
        </div>

        {{-- 🔹 Ingredients & Instructions --}}
        <h5 class="section-title">Ingredients & Instructions</h5>

        {{-- 🔹 Ingredients --}}
        <div class="mb-4">
            <label class="form-label fw-semibold">Ingredients <span class="text-danger">*</span></label>
            <div id="ingredients-wrapper">
                @php
                    $ingredients = old('ingredients', isset($recipe) && is_array($recipe->ingredients) ? $recipe->ingredients : ($recipe->ingredients ? json_decode($recipe->ingredients, true) : ['']));
                @endphp
                @foreach ($ingredients as $index => $ing)
                    <div class="input-group mb-2 ingredient-item">
                        <input type="text" name="ingredients[]" class="form-control" value="{{ $ing }}" placeholder="Enter ingredient" required>
                        <button type="button" class="btn btn-outline-danger remove-ingredient" {{ $loop->first ? 'disabled' : '' }}>
                            <i class="ti-minus"></i>
                        </button>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-outline-success btn-sm" id="add-ingredient">
                <i class="ti-plus"></i> Add Ingredient
            </button>
        </div>

        {{-- 🔹 Instructions --}}
        <div class="mb-4">
            <label class="form-label fw-semibold">Instructions <span class="text-danger">*</span></label>
            <div id="instructions-wrapper">
                @php
                    $instructions = old('instructions', isset($recipe) && is_array($recipe->instructions) ? $recipe->instructions : ($recipe->instructions ? json_decode($recipe->instructions, true) : ['']));
                @endphp
                @foreach ($instructions as $index => $step)
                    <div class="input-group mb-2 instruction-item">
                        <input type="text" name="instructions[]" class="form-control" value="{{ $step }}" placeholder="Enter instruction" required>
                        <button type="button" class="btn btn-outline-danger remove-instruction" {{ $loop->first ? 'disabled' : '' }}>
                            <i class="ti-minus"></i>
                        </button>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-outline-success btn-sm" id="add-instruction">
                <i class="ti-plus"></i> Add Instruction
            </button>
        </div>

        {{-- 🔹 Nutrition Info --}}
        <h5 class="section-title">Nutritional Info</h5>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Calories <span class="text-danger">*</span></label>
                <input type="number" name="calories" class="form-control" value="{{ old('calories', $recipe->calories ?? '') }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Protein (g)</label>
                <input type="number" name="protein" step="0.1" class="form-control" value="{{ old('protein', $recipe->protein ?? '') }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Carbs (g)</label>
                <input type="number" name="carbs" step="0.1" class="form-control" value="{{ old('carbs', $recipe->carbs ?? '') }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Fat (g)</label>
                <input type="number" name="fat" step="0.1" class="form-control" value="{{ old('fat', $recipe->fat ?? '') }}">
            </div>
        </div>

        {{-- 🔹 Timing & Tags --}}
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Prep Time (min)</label>
                <input type="number" name="prep_time" class="form-control" value="{{ old('prep_time', $recipe->prep_time ?? 0) }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Cook Time (min)</label>
                <input type="number" name="cook_time" class="form-control" value="{{ old('cook_time', $recipe->cook_time ?? 0) }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Servings</label>
                <input type="number" name="servings" class="form-control" value="{{ old('servings', $recipe->servings ?? 1) }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Tags (comma-separated)</label>
                <input type="text" name="tags" class="form-control" placeholder="e.g. vegan, gluten-free" value="{{ old('tags', is_array($recipe->tags ?? null) ? implode(', ', $recipe->tags) : $recipe->tags ?? '') }}">
            </div>
        </div>

        {{-- 🔹 Image Upload & Premium --}}
        <h5 class="section-title">Image & Options</h5>
        <div class="row align-items-center">
            <div class="col-md-4 mb-3">
                <label class="form-label">Recipe Image</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
                <small class="text-muted">Upload jpg/png (max 2MB)</small>
            </div>
            <div class="col-md-4 mb-3">
                <img id="preview" class="image-preview" 
                     src="{{ !empty($recipe->image_url) ? $recipe->image_url : asset('assets/images/default-meal.jpg') }}" 
                     alt="Recipe Image">
            </div>
            <div class="col-md-4 mb-3">
                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" name="is_premium" value="1"
                        {{ old('is_premium', $recipe->is_premium ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label">Mark as Premium Recipe</label>
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

document.addEventListener('DOMContentLoaded', () => {
    // Ingredients
    const ingWrapper = document.getElementById('ingredients-wrapper');
    const addIngBtn = document.getElementById('add-ingredient');
    addIngBtn.addEventListener('click', () => {
        const newField = document.createElement('div');
        newField.className = 'input-group mb-2 ingredient-item';
        newField.innerHTML = `
            <input type="text" name="ingredients[]" class="form-control" placeholder="Enter ingredient" required>
            <button type="button" class="btn btn-outline-danger remove-ingredient"><i class="ti-minus"></i></button>
        `;
        ingWrapper.appendChild(newField);
        refreshIngredientButtons();
    });
    ingWrapper.addEventListener('click', e => {
        if (e.target.closest('.remove-ingredient')) {
            e.target.closest('.ingredient-item').remove();
            refreshIngredientButtons();
        }
    });
    function refreshIngredientButtons() {
        const buttons = ingWrapper.querySelectorAll('.remove-ingredient');
        buttons.forEach((btn, i) => btn.disabled = (i === 0));
    }

    // Instructions
    const instWrapper = document.getElementById('instructions-wrapper');
    const addInstBtn = document.getElementById('add-instruction');
    addInstBtn.addEventListener('click', () => {
        const newField = document.createElement('div');
        newField.className = 'input-group mb-2 instruction-item';
        newField.innerHTML = `
            <input type="text" name="instructions[]" class="form-control" placeholder="Enter instruction" required>
            <button type="button" class="btn btn-outline-danger remove-instruction"><i class="ti-minus"></i></button>
        `;
        instWrapper.appendChild(newField);
        refreshInstructionButtons();
    });
    instWrapper.addEventListener('click', e => {
        if (e.target.closest('.remove-instruction')) {
            e.target.closest('.instruction-item').remove();
            refreshInstructionButtons();
        }
    });
    function refreshInstructionButtons() {
        const buttons = instWrapper.querySelectorAll('.remove-instruction');
        buttons.forEach((btn, i) => btn.disabled = (i === 0));
    }
});
</script>
@endpush