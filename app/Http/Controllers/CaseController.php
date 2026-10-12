<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCaseRequest;
use App\Http\Requests\UpdateCaseRequest;
use App\Models\MedicalCase;
use App\Services\HolidayService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CaseController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of medical cases with pagination (10/page), search, filters, and sorting.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', MedicalCase::class);

        $search = $request->query('search');
        $status = $request->query('status');
        $priority = $request->query('priority');
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowedSortColumns = ['case_number', 'patient_reference', 'surgeon_name', 'status', 'priority', 'surgery_date', 'created_at'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }

        $query = MedicalCase::with('creator')
            ->search($search)
            ->filterStatus($status)
            ->filterPriority($priority)
            ->orderBy($sortBy, $sortDir);

        $cases = $query->paginate(10)->withQueryString();

        return view('cases.index', [
            'cases' => $cases,
            'search' => $search,
            'status' => $status,
            'priority' => $priority,
            'sortBy' => $sortBy,
            'sortDir' => $sortDir,
            'statuses' => MedicalCase::STATUSES,
            'priorities' => MedicalCase::PRIORITIES,
        ]);
    }

    /**
     * Show the form for creating a new medical case.
     */
    public function create(HolidayService $holidayService): View
    {
        $this->authorize('create', MedicalCase::class);

        $upcomingHolidays = $holidayService->getUpcomingHolidays();

        return view('cases.create', [
            'case' => new MedicalCase(),
            'statuses' => MedicalCase::STATUSES,
            'priorities' => MedicalCase::PRIORITIES,
            'upcomingHolidays' => $upcomingHolidays,
        ]);
    }

    /**
     * Store a newly created medical case in storage.
     */
    public function store(StoreCaseRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = Auth::id();

        $case = MedicalCase::create($validated);

        return redirect()->route('cases.show', $case)
            ->with('success', "Medical case #{$case->case_number} was successfully created.");
    }

    /**
     * Display the specified medical case details.
     */
    public function show(MedicalCase $case, HolidayService $holidayService): View
    {
        $this->authorize('view', $case);

        $case->load('creator');

        $holiday = null;
        if ($case->surgery_date) {
            $holiday = $holidayService->checkDateHoliday($case->surgery_date->format('Y-m-d'));
        }

        return view('cases.show', [
            'case' => $case,
            'holiday' => $holiday,
        ]);
    }

    /**
     * Show the form for editing the specified medical case.
     */
    public function edit(MedicalCase $case, HolidayService $holidayService): View
    {
        $this->authorize('update', $case);

        $upcomingHolidays = $holidayService->getUpcomingHolidays();

        return view('cases.edit', [
            'case' => $case,
            'statuses' => MedicalCase::STATUSES,
            'priorities' => MedicalCase::PRIORITIES,
            'upcomingHolidays' => $upcomingHolidays,
        ]);
    }

    /**
     * Update the specified medical case in storage.
     */
    public function update(UpdateCaseRequest $request, MedicalCase $case): RedirectResponse
    {
        $this->authorize('update', $case);

        $case->update($request->validated());

        return redirect()->route('cases.show', $case)
            ->with('success', "Medical case #{$case->case_number} was successfully updated.");
    }

    /**
     * Remove the specified medical case from storage.
     * Strictly Admin only. Blocked with 403 Forbidden for Viewer or Engineer.
     */
    public function destroy(MedicalCase $case): RedirectResponse
    {
        $this->authorize('delete', $case);

        $caseNumber = $case->case_number;
        $case->delete();

        return redirect()->route('cases.index')
            ->with('success', "Medical case #{$caseNumber} has been successfully deleted.");
    }
}
