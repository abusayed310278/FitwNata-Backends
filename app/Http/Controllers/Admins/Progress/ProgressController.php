<?php

namespace App\Http\Controllers\Admins\Progress;

use App\Http\Controllers\Controller;
use App\Models\ProgressJournal;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ProgressController extends Controller
{
    public function index()
    {
        $totalEntries = ProgressJournal::count();
        $weekEntries = ProgressJournal::where('entry_date', '>=', Carbon::now()->startOfWeek())->count();
        $coachNotes = ProgressJournal::where('entry_type', 'coach_note')->count();
        $activeUsers = ProgressJournal::distinct('user_id')->count('user_id');
        return view('admins.progress.index', compact('totalEntries', 'weekEntries', 'coachNotes', 'activeUsers'));
    }

    public function show(ProgressJournal $progress)
    {
        $entry = $progress->load(['user', 'coach']);
        return view('admins.progress.show', compact('entry'));
    }

    public function destroy(ProgressJournal $progress)
    {
        $progress->delete();
        return redirect()->route('progress.index')
            ->with('success', 'Progress journal entry deleted successfully!');
    }

    public function getProgressJournals(Request $request)
    {
        if ($request->ajax()) {
            $query = ProgressJournal::with(['user', 'coach']);

            // Filter by entry type if provided
            if ($request->entry_type) {
                $query->where('entry_type', $request->entry_type);
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
                ->editColumn('entry_type', function($row) {
                    $colors = [
                        'workout' => 'primary',
                        'nutrition' => 'success',
                        'wellness' => 'info',
                        'measurements' => 'warning',
                        'goals' => 'secondary',
                        'coach_note' => 'danger'
                    ];
                    $color = $colors[$row->entry_type] ?? 'secondary';
                    return '<span class="badge bg-'.$color.'">'.ucfirst(str_replace('_', ' ', $row->entry_type)).'</span>';
                })
                ->editColumn('title', function($row) {
                    return $row->title ?: '<span class="text-muted">No title</span>';
                })
                ->editColumn('entry_date', function($row) {
                    return $row->entry_date->format('M d, Y');
                })
                ->editColumn('mood_rating', function($row) {
                    if (!$row->mood_rating) return '<span class="text-muted">-</span>';
                    $stars = '';
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $row->mood_rating) {
                            $stars .= '<i class="ti-star text-warning"></i>';
                        } else {
                            $stars .= '<i class="ti-star text-muted"></i>';
                        }
                    }
                    return $stars;
                })
                ->editColumn('energy_level', function($row) {
                    if (!$row->energy_level) return '<span class="text-muted">-</span>';
                    $bolts = '';
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $row->energy_level) {
                            $bolts .= '<i class="ti-bolt text-primary"></i>';
                        } else {
                            $bolts .= '<i class="ti-bolt text-muted"></i>';
                        }
                    }
                    return $bolts;
                })
                ->editColumn('coach', function($row) {
                    if (!$row->coach) return '<span class="text-muted">-</span>';
                    return '
                        <div>
                            <strong>'.$row->coach->name.'</strong><br>
                            <small class="text-muted">Coach</small>
                        </div>';
                })
                ->addColumn('actions', function($row) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><button class="dropdown-item" onclick="viewEntry('.$row->id.')">
                                    <i class="ti-eye"></i> View
                                </button></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><button class="dropdown-item text-danger" onclick="deleteEntry('.$row->id.')">
                                    <i class="ti-trash"></i> Delete
                                </button></li>
                            </ul>
                        </div>';
                })
                ->rawColumns(['user', 'entry_type', 'title', 'mood_rating', 'energy_level', 'coach', 'actions'])
                ->make(true);
        }
    }
}
