<?php

namespace App\Http\Controllers;

use App\Models\MedicalCase;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the medical implant case management dashboard.
     */
    public function index(): View
    {
        $totalCases = MedicalCase::count();
        $planningCases = MedicalCase::where('status', MedicalCase::STATUS_PLANNING)->count();
        $inReviewCases = MedicalCase::where('status', MedicalCase::STATUS_IN_REVIEW)->count();
        $approvedCases = MedicalCase::where('status', MedicalCase::STATUS_APPROVED)->count();
        $completedCases = MedicalCase::where('status', MedicalCase::STATUS_COMPLETED)->count();
        $draftCases = MedicalCase::where('status', MedicalCase::STATUS_DRAFT)->count();
        $cancelledCases = MedicalCase::where('status', MedicalCase::STATUS_CANCELLED)->count();

        $recentCases = MedicalCase::with('creator')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', [
            'totalCases' => $totalCases,
            'planningCases' => $planningCases,
            'inReviewCases' => $inReviewCases,
            'approvedCases' => $approvedCases,
            'completedCases' => $completedCases,
            'draftCases' => $draftCases,
            'cancelledCases' => $cancelledCases,
            'recentCases' => $recentCases,
        ]);
    }
}
