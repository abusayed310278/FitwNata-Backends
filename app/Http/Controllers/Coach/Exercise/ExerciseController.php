<?php

namespace App\Http\Controllers\Coach\Exercise;

use App\Models\Exercise;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ExerciseController extends Controller
{
    public function index()
    {
        return view('coach.exercise.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('coach.exercise.create');
    }

   /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:exercises,name',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'equipment_needed' => 'nullable|string|max:255',
            'calories_per_rep_or_second' => 'nullable|numeric|min:0',
            'instructions' => 'nullable|array',
            'instructions.*' => 'nullable|string|max:1000',
            'tips' => 'nullable|array',
            'tips.*' => 'nullable|string|max:1000',
        ]);

        // Convert array to comma-separated string
        $validated['instructions'] = $request->instructions ? implode(',', $request->instructions) : null;
        $validated['tips'] = $request->tips ? implode(',', $request->tips) : null;
        $validated['user_id'] = Auth::Id();

        Exercise::create($validated);

        return redirect()->route('coach.exercise.index')
            ->with('success', 'Exercise created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exercise $exercise)
    {
        return view('coach.exercise.edit', compact('exercise'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exercise $exercise)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:exercises,name,' . $exercise->id,
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'equipment_needed' => 'nullable|string|max:255',
            'calories_per_rep_or_second' => 'nullable|numeric|min:0',
            'instructions' => 'nullable|array',
            'instructions.*' => 'nullable|string|max:1000',
            'tips' => 'nullable|array',
            'tips.*' => 'nullable|string|max:1000',
        ]);

        // Convert array to comma-separated string
        $validated['instructions'] = $request->instructions ? implode(',', $request->instructions) : null;
        $validated['tips'] = $request->tips ? implode(',', $request->tips) : null;

        $exercise->update($validated);

        return redirect()->route('coach.exercise.index')
            ->with('success', 'Exercise updated successfully!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exercise $exercise)
    {
        // Detach from any workouts to avoid foreign key constraint issues
        $exercise->workouts()->detach();
        $exercise->delete();

        return redirect()->route('coach.exercise.index')
            ->with('success', 'Exercise deleted successfully!');
    }

    /**
     * Provide data for DataTables.
     */
    public function getExercises(Request $request)
    {
        if ($request->ajax()) {
            $exercises = Exercise::where('user_id', Auth::Id());

            return DataTables::of($exercises)
                ->addIndexColumn()
                ->addColumn('checkbox', fn($row) => '<input type="checkbox" name="exercise_id[]" value="'.$row->id.'">')
                ->editColumn('name', fn($row) => '
                    <div>
                        <strong>'.$row->name.'</strong><br>
                        <small class="text-muted">'.Str::limit($row->description, 70).'</small>
                    </div>'
                )
                ->addColumn('actions', fn($row) => '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">Actions</button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="'.route('coach.exercise.edit', $row->id).'"><i class="ti ti-pencil"></i> Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="'.route('coach.exercise.destroy', $row->id).'" method="POST">
                                    '.csrf_field().method_field('DELETE').'
                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm(\'Are you sure? This will remove the exercise from all workouts.\')">
                                        <i class="ti ti-trash"></i> Delete
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>'
                )
                ->rawColumns(['checkbox', 'name', 'actions'])
                ->make(true);
        }
    }
}
