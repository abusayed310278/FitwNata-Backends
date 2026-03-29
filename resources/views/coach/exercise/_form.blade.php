@csrf
<div class="mb-3">
    <label for="name" class="form-label">Exercise Name</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
        value="{{ old('name', $exercise->name ?? '') }}" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
        rows="4">{{ old('description', $exercise->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="video_url" class="form-label">YouTube Video URL (Optional)</label>
    <input type="url" class="form-control @error('video_url') is-invalid @enderror" id="video_url" name="video_url"
        value="{{ old('video_url', $exercise->video_url ?? '') }}" placeholder="https://www.youtube.com/watch?v=...">
    @error('video_url')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="equipment_needed" class="form-label">Equipment Needed (Optional: Dumbbells, Yoga Mat, None )</label>
    <input type="text" class="form-control @error('equipment_needed') is-invalid @enderror" id="equipment_needed"
        name="equipment_needed" value="{{ old('equipment_needed', $exercise->equipment_needed ?? '') }}"
        placeholder="e.g. Dumbbells, Yoga Mat, None">
    @error('equipment_needed')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="calories_per_rep_or_second" class="form-label">Calories Burned (per rep or second)</label>
    <input type="number" step="0.01" class="form-control @error('calories_per_rep_or_second') is-invalid @enderror"
        id="calories_per_rep_or_second" name="calories_per_rep_or_second"
        value="{{ old('calories_per_rep_or_second', $exercise->calories_per_rep_or_second ?? '') }}">
    @error('calories_per_rep_or_second')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Instructions</label>
    <div id="instructions-wrapper">
        @php
            $instructions = old(
                'instructions',
                isset($exercise->instructions) ? explode(',', $exercise->instructions) : [],
            );
        @endphp
        @foreach ($instructions as $instruction)
            <div class="input-group mb-2">
                <input type="text" name="instructions[]" class="form-control" value="{{ trim($instruction) }}"
                    placeholder="Enter instruction">
                <button type="button" class="btn btn-danger remove-instruction">X</button>
            </div>
        @endforeach
        @if (empty($instructions))
            <div class="input-group mb-2">
                <input type="text" name="instructions[]" class="form-control" placeholder="Enter instruction">
                <button type="button" class="btn btn-danger remove-instruction">X</button>
            </div>
        @endif
    </div>
    <button type="button" id="add-instruction" class="btn btn-sm btn-primary">+ Add Instruction</button>
</div>


<div class="mb-3">
    <label class="form-label">Tips</label>
    <div id="tips-wrapper">
        @php
            $tips = old('tips', isset($exercise->tips) ? explode(',', $exercise->tips) : []);
        @endphp
        @foreach ($tips as $tip)
            <div class="input-group mb-2">
                <input type="text" name="tips[]" class="form-control" value="{{ trim($tip) }}"
                    placeholder="Enter tip">
                <button type="button" class="btn btn-danger remove-tip">X</button>
            </div>
        @endforeach
        @if (empty($tips))
            <div class="input-group mb-2">
                <input type="text" name="tips[]" class="form-control" placeholder="Enter tip">
                <button type="button" class="btn btn-danger remove-tip">X</button>
            </div>
        @endif
    </div>
    <button type="button" id="add-tip" class="btn btn-sm btn-primary">+ Add Tip</button>
</div>


@push('scripts')
    <script>
        document.getElementById('add-instruction').addEventListener('click', function() {
            let wrapper = document.getElementById('instructions-wrapper');
            let div = document.createElement('div');
            div.classList.add('input-group', 'mb-2');
            div.innerHTML = `<input type="text" name="instructions[]" class="form-control" placeholder="Enter instruction">
                     <button type="button" class="btn btn-danger remove-instruction">X</button>`;
            wrapper.appendChild(div);
        });

        document.getElementById('add-tip').addEventListener('click', function() {
            let wrapper = document.getElementById('tips-wrapper');
            let div = document.createElement('div');
            div.classList.add('input-group', 'mb-2');
            div.innerHTML = `<input type="text" name="tips[]" class="form-control" placeholder="Enter tip">
                     <button type="button" class="btn btn-danger remove-tip">X</button>`;
            wrapper.appendChild(div);
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-instruction')) {
                e.target.parentElement.remove();
            }
            if (e.target.classList.contains('remove-tip')) {
                e.target.parentElement.remove();
            }
        });
    </script>
@endpush
