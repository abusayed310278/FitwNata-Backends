<?php

namespace App\Http\Controllers\Coach\Workout;

use App\Models\Tag;
use App\Models\Plan;
use App\Models\Workout;
use App\Models\Exercise;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class WorkoutController extends Controller
{
    public function index()
    {
        return view('coach.workout.index');
    }

    public function create()
    {
        $tags = Tag::get();
        $exercises = Exercise::where('user_id', Auth::Id())->get();
        return view('coach.workout.create', get_defined_vars());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'duration_minutes' => 'required|integer|min:1',
            'calories_burned' => 'nullable|integer|min:0',
            'thumbnail_url' => 'nullable|url',
            'tips' => 'nullable',
            'instructions' => 'nullable',

            // exercises
            'exercises' => 'array',
            'exercises.*.exercise_id' => 'required|exists:exercises,id',
            'exercises.*.sets' => 'nullable|integer|min:1',
            'exercises.*.reps' => 'nullable|integer|min:1',
            'exercises.*.duration_seconds' => 'nullable|integer|min:1',
            'exercises.*.order' => 'required|integer|min:1',

            // tags
            'tags.*'=> 'exists:tags,id',

            // publish
            'published_at' => 'required|date',

             // ✅ new required fields
            'fitness_goals' => 'required|string',
            'training_location' => 'required|string',
            'health_conditions' => 'required|string',
            'gender_preference' => 'required|string',
            'equipment' => 'required|string',
            'type' => 'required|string',
            'plan_type' => 'required|in:free,premium',
        ]);

        $validated['is_premium'] = $request->plan_type == 'free' ? 0 : 1;
        $validated['duration'] = $request->duration_minutes;
        $validated['duration_minutes']= (int) $request->duration_minutes;
        $validated['user_id'] = Auth::Id();
        $workout = Workout::create($validated);

        if ($request->has('tags')) {
            $workout->tags()->sync($request->tags);
        }
        try{
            // if (isset($validated['exercises'])) {
            //     foreach ($validated['exercises'] as $exercise) {
            //         $workout->exercises()->attach($exercise['exercise_id'], [
            //             'sets' => (int)$exercise['sets'] ?? null,
            //             'reps' => (int)$exercise['reps'] ?? null,
            //             'duration_seconds' => (int)$exercise['duration_seconds'] ?? null,
            //             'order' => (int)$exercise['order'] ?? $i+1,
            //         ]);
            //     }
            // }

            if ($request->filled('exercises')) {
                foreach ($request->input('exercises', []) as $i => $ex) {
                    $workout->exercises()->attach(
                        (int) $ex['exercise_id'],
                        [
                            'sets'             => array_key_exists('sets', $ex) ? (int) $ex['sets'] : null,
                            'reps'             => array_key_exists('reps', $ex) ? (int) $ex['reps'] : null,
                            'duration_seconds' => array_key_exists('duration_seconds', $ex) ? (int) $ex['duration_seconds'] : null,
                            'order'            => array_key_exists('order', $ex) ? (int) $ex['order'] : ($i + 1),
                        ]
                    );
                }
            }

            return redirect()->route('coach.workout.index')
                ->with('success', 'Workout created successfully!');
        }catch(\Exception $e)
        {
            \Log::error($e);
          return redirect()->route('coach.workout.index')
                ->with('error', 'There is something wrong');
        }

    }

    public function show(Workout $workout)
    {
        $plans = Plan::get();
        $workout->load('exercises');
        return view('coach.workout.show',get_defined_vars());
    }

    public function edit(Workout $workout)
    {
        $tags = Tag::get();
        $selectedTags = $workout->tags->pluck('id')->toArray();
        $exercises = Exercise::all();
        $workout->load('exercises');
        return view('coach.workout.edit', get_defined_vars());
    }

    public function update(Request $request, Workout $workout)
    {
       $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'duration_minutes' => 'required|integer|min:1',
            'calories_burned' => 'nullable|integer|min:0',
            'thumbnail_url' => 'nullable|url',
            'tips' => 'nullable',
            'instructions' => 'nullable',

            // exercises
            'exercises' => 'array',
            'exercises.*.exercise_id' => 'required|exists:exercises,id',
            'exercises.*.sets' => 'nullable|integer|min:1',
            'exercises.*.reps' => 'nullable|integer|min:1',
            'exercises.*.duration_seconds' => 'nullable|integer|min:1',
            'exercises.*.order' => 'required|integer|min:1',

            // tags
            'tags.*'=> 'exists:tags,id',

            // publish
            'published_at' => 'required|date',

            // ✅ new fields
           // ✅ new required fields
            'fitness_goals' => 'required|string',
            'training_location' => 'required|string',
            'health_conditions' => 'required|string',
            'gender_preference' => 'required|string',
            'equipment' => 'required|string',
            'type' => 'required|string',
            'plan_type' => 'required|in:free,premium',
        ]);

        $validated['is_premium'] = $request->plan_type == 'free' ? 0 : 1;
        $validated['duration'] = $request->duration_minutes;
        $validated['duration_minutes']= (int) $request->duration_minutes;

        // dd($validated);
        $workout->update($validated);

        if ($request->has('tags')) {
            $workout->tags()->sync($request->tags);
        }

         try{
            // if (isset($validated['exercises'])) {
            //             $data = [];
            //         foreach ($validated['exercises'] as $i => $exercise) {
            //             $data[$exercise['exercise_id']] = [
            //                 'sets' => (int)$exercise['sets'] ?? null,
            //                 'reps' => (int)$exercise['reps'] ?? null,
            //                 'duration_seconds' => (int)$exercise['duration_seconds'] ?? null,
            //                 'order' => (int)$exercise['order'] ?? $i+1,
            //             ];
            //         }

            //         $workout->exercises()->sync($data);
            // }

            if ($request->filled('exercises')) {
                $data = [];
                foreach ($request->input('exercises', []) as $i => $ex) {
                    $data[(int) $ex['exercise_id']] = [
                        'sets'             => array_key_exists('sets', $ex) ? (int) $ex['sets'] : null,
                        'reps'             => array_key_exists('reps', $ex) ? (int) $ex['reps'] : null,
                        'duration_seconds' => array_key_exists('duration_seconds', $ex) ? (int) $ex['duration_seconds'] : null,
                        'order'            => array_key_exists('order', $ex) ? (int) $ex['order'] : ($i + 1),
                    ];
                }
                $workout->exercises()->sync($data);
            }

            return redirect()->route('coach.workout.index')
                ->with('success', 'Workout updated successfully!');
        }catch(\Exception $e)
        {
            \Log::error($e);
          return redirect()->route('coach.workout.index')
                ->with('error', 'There is something wrong');
        }
    }

    public function destroy(Workout $workout)
    {
        $workout->delete();
        return redirect()->route('coach.workout.index')
            ->with('success', 'Workout deleted successfully!');
    }

    public function getWorkouts(Request $request)
    {
        if ($request->ajax()) {
            $workouts = Workout::withCount('exercises')->where('user_id', Auth::Id())->orderBy('id', 'DESC');

            return DataTables::of($workouts)
                ->addIndexColumn()
                ->addColumn('checkbox', function($row) {
                    return '<input type="checkbox" name="workout_id[]" value="'.$row->id.'">';
                })
                ->editColumn('title', function($row) {
                    return '
                        <div>
                            <strong>'.$row->title.'</strong><br>
                            <small class="text-muted">'.Str::limit($row->description, 50).'</small>
                        </div>';
                })
                ->editColumn('level', function($row) {
                    $colors = [
                        'beginner' => 'success',
                        'intermediate' => 'warning',
                        'advanced' => 'danger'
                    ];
                    return '<span class="badge bg-'.$colors[$row->level].'">'.ucfirst($row->level).'</span>';
                })
                ->editColumn('duration_minutes', function($row) {
                    return $row->duration_minutes . ' min';
                })
                ->addColumn('exercises_count', function($row) {
                    return '<span class="badge bg-info">'.$row->exercises_count.' exercises</span>';
                })
                ->addColumn('actions', function($row) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="'.route('coach.workout.show', $row->id).'">
                                    <i class="ti-eye"></i> View
                                </a></li>
                                <li><a class="dropdown-item" href="'.route('coach.workout.edit', $row->id).'">
                                    <i class="ti-pencil"></i> Edit
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="'.route('coach.workout.destroy', $row->id).'" method="POST" class="d-inline">
                                        '.csrf_field().'
                                        '.method_field('DELETE').'
                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm(\'Are you sure?\')">
                                            <i class="ti-trash"></i> Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>';
                })
                ->rawColumns(['checkbox', 'title', 'level', 'exercises_count', 'actions'])
                ->make(true);
        }
    }
}
