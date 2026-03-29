@extends('layouts.adminApp')

@section('title', 'Assign Recipes to ' . $mealPlan->title)

@push('styles')
<style>
    .section-title { font-weight: 600; margin: 1.5rem 0 1rem; color: #2c3e50; display:flex; align-items:center; justify-content:space-between; }
    .day-header { background: #f8f9fa; padding: 8px 12px; border-radius: 6px; font-weight: 600; }
    .table td select { min-width: 200px; }
    .copy-btn { font-size: 0.85rem; padding: 4px 10px; }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h3 class="page-title">Assign Recipes — {{ $mealPlan->title }}</h3>
        <a href="{{ route('nutritionist.mealplans.show', $mealPlan->id) }}" class="btn btn-secondary">
            <i class="ti-arrow-left"></i> Back
        </a>
    </div>

    <form action="{{ route('nutritionist.mealplans.assign.store', $mealPlan->id) }}" method="POST">
        @csrf

        @php
            $daysOfWeek = [1=>'Monday', 2=>'Tuesday', 3=>'Wednesday', 4=>'Thursday', 5=>'Friday', 6=>'Saturday', 7=>'Sunday'];
            $mealTypes = ['breakfast', 'lunch', 'dinner', 'snack'];
            $existing = collect($existingAssignments);
        @endphp

        @foreach($daysOfWeek as $dayNumber => $dayName)
            <div class="section-title">
                <span><i class="ti-calendar text-primary"></i> {{ $dayName }}</span>

                {{-- Copy from previous day --}}
                @if($dayNumber > 1)
                    <button type="button" 
                            class="btn btn-outline-info btn-sm copy-btn"
                            onclick="copyFromPreviousDay({{ $dayNumber }})">
                        <i class="ti-copy"></i> Copy from {{ $daysOfWeek[$dayNumber - 1] }}
                    </button>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-4">
                    <thead>
                        <tr>
                            <th style="width:25%">Meal Type</th>
                            <th>Recipe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mealTypes as $mealType)
                            @php
                                $assigned = $existing->firstWhere(fn($a) => $a['day_of_week']==$dayNumber && $a['meal_type']==$mealType);
                            @endphp
                            <tr>
                                <td class="text-capitalize fw-semibold">{{ $mealType }}</td>
                                <td>
                                    <select name="assignments[{{ $dayNumber }}_{{ $mealType }}][recipe_id]" 
                                            class="form-select recipe-select"
                                            data-day="{{ $dayNumber }}" 
                                            data-meal="{{ $mealType }}">
                                        <option value="">— Select Recipe —</option>
                                        @foreach($recipes as $recipe)
                                            <option value="{{ $recipe->id }}"
                                                {{ isset($assigned['recipe_id']) && $assigned['recipe_id']==$recipe->id ? 'selected' : '' }}>
                                                {{ $recipe->title }} ({{ $recipe->calories }} kcal)
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="assignments[{{ $dayNumber }}_{{ $mealType }}][day_of_week]" value="{{ $dayNumber }}">
                                    <input type="hidden" name="assignments[{{ $dayNumber }}_{{ $mealType }}][meal_type]" value="{{ $mealType }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="ti-save"></i> Save Assignments
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function copyFromPreviousDay(currentDay) {
    const prevDay = currentDay - 1;

    const prevSelects = document.querySelectorAll(`select.recipe-select[data-day="${prevDay}"]`);
    const currentSelects = document.querySelectorAll(`select.recipe-select[data-day="${currentDay}"]`);

    prevSelects.forEach((prevSelect, index) => {
        const currentSelect = currentSelects[index];
        if (prevSelect && currentSelect) {
            currentSelect.value = prevSelect.value;
        }
    });

    // Visual feedback
    const button = event.target.closest('button');
    button.classList.remove('btn-outline-info');
    button.classList.add('btn-success');
    button.innerHTML = '<i class="ti-check"></i> Copied!';
    setTimeout(() => {
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-info');
        button.innerHTML = `<i class="ti-copy"></i> Copy from previous day`;
    }, 1500);
}
</script>
@endpush