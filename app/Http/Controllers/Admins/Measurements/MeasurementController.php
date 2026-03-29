<?php

namespace App\Http\Controllers\Admins\Measurements;

use App\Http\Controllers\Controller;
use App\Models\UserMeasurement;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class MeasurementController extends Controller
{
    public function index()
    {
        $totalMeasurements = UserMeasurement::count();
        $weekMeasurements = UserMeasurement::where('date', '>=', Carbon::now()->startOfWeek())->count();
        $activeMeasurementUsers = UserMeasurement::distinct('user_id')->count('user_id');
        $avgWeight = UserMeasurement::whereNotNull('weight')->avg('weight') ?:
                    UserMeasurement::whereNotNull('weight_kg')->avg('weight_kg') ?: 0;

        return view('admins.measurements.index', compact(
            'totalMeasurements',
            'weekMeasurements',
            'activeMeasurementUsers',
            'avgWeight'
        ));
    }

    public function show(UserMeasurement $measurement)
    {
        $measurement->load('user');
        return view('admins.measurements.show', compact('measurement'));
    }

    public function destroy(UserMeasurement $measurement)
    {
        $measurement->delete();
        return redirect()->route('measurements.index')
            ->with('success', 'Measurement deleted successfully!');
    }

    public function userProgress(User $user)
    {
        $measurements = $user->measurements()->orderBy('date', 'desc')->get();
        $totalMeasurements = $measurements->count();
        $latestMeasurement = $measurements->first();

        // Calculate changes from first to latest measurement
        $firstMeasurement = $measurements->last();
        $weightChange = null;
        $bodyFatChange = null;
        $muscleMassChange = null;

        if ($latestMeasurement && $firstMeasurement && $latestMeasurement->id !== $firstMeasurement->id) {
            $latestWeight = $latestMeasurement->weight ?: $latestMeasurement->weight_kg;
            $firstWeight = $firstMeasurement->weight ?: $firstMeasurement->weight_kg;

            if ($latestWeight && $firstWeight) {
                $weightChange = $latestWeight - $firstWeight;
            }

            if ($latestMeasurement->body_fat_percentage && $firstMeasurement->body_fat_percentage) {
                $bodyFatChange = $latestMeasurement->body_fat_percentage - $firstMeasurement->body_fat_percentage;
            }

            if ($latestMeasurement->muscle_mass && $firstMeasurement->muscle_mass) {
                $muscleMassChange = $latestMeasurement->muscle_mass - $firstMeasurement->muscle_mass;
            }
        }

        return view('admins.measurements.user-progress', compact(
            'user',
            'measurements',
            'totalMeasurements',
            'latestMeasurement',
            'weightChange',
            'bodyFatChange',
            'muscleMassChange'
        ));
    }

    public function getMeasurements(Request $request)
    {
        if ($request->ajax()) {
            $query = UserMeasurement::with('user');

            // Date filter
            if ($request->date_from) {
                $query->where('date', '>=', $request->date_from);
            }
            if ($request->date_to) {
                $query->where('date', '<=', $request->date_to);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('user', function($row) {
                    return '
                        <div>
                            <strong>'.$row->user->name.'</strong><br>
                            <small class="text-muted">'.$row->user->email.'</small>
                        </div>';
                })
                ->editColumn('date', function($row) {
                    return $row->date->format('M d, Y');
                })
                ->editColumn('weight', function($row) {
                    $weight = $row->weight ?: $row->weight_kg;
                    return $weight ? $weight . ' kg' : '<span class="text-muted">-</span>';
                })
                ->editColumn('height', function($row) {
                    return $row->height ? $row->height . ' cm' : '<span class="text-muted">-</span>';
                })
                ->editColumn('body_fat_percentage', function($row) {
                    return $row->body_fat_percentage ? $row->body_fat_percentage . '%' : '<span class="text-muted">-</span>';
                })
                ->editColumn('muscle_mass', function($row) {
                    return $row->muscle_mass ? $row->muscle_mass . ' kg' : '<span class="text-muted">-</span>';
                })
                ->editColumn('bmi', function($row) {
                    if (!$row->bmi) return '<span class="text-muted">-</span>';

                    $bmi = $row->bmi;
                    $category = '';
                    $color = '';

                    if ($bmi < 18.5) { $category = 'Underweight'; $color = 'info'; }
                    elseif ($bmi < 25) { $category = 'Normal'; $color = 'success'; }
                    elseif ($bmi < 30) { $category = 'Overweight'; $color = 'warning'; }
                    else { $category = 'Obese'; $color = 'danger'; }

                    return '
                        <div>
                            <strong>'.number_format($bmi, 1).'</strong><br>
                            <span class="badge bg-'.$color.'">'.$category.'</span>
                        </div>';
                })
                ->addColumn('actions', function($row) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><button class="dropdown-item" onclick="viewMeasurement('.$row->id.')">
                                    <i class="ti-eye"></i> View
                                </button></li>
                                <li><button class="dropdown-item" onclick="viewUserProgress('.$row->user_id.')">
                                    <i class="ti-chart-line"></i> User Progress
                                </button></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><button class="dropdown-item text-danger" onclick="deleteMeasurement('.$row->id.')">
                                    <i class="ti-trash"></i> Delete
                                </button></li>
                            </ul>
                        </div>';
                })
                ->rawColumns(['user', 'weight', 'height', 'body_fat_percentage', 'muscle_mass', 'bmi', 'actions'])
                ->make(true);
        }
    }
}
