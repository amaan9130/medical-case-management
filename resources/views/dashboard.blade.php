@extends('layouts.app', ['title' => 'Clinical Dashboard'])

@section('content')
<div class="space-y-6">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Clinical Implant Dashboard</h1>
            <p class="text-sm text-slate-500 mt-1">Real-time status overview of custom and standard orthopedic implant cases.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('cases.index') }}" 
               class="px-4 py-2 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-lg shadow-2xs transition">
                Browse All Cases
            </a>
            @can('create', App\Models\MedicalCase::class)
            <a href="{{ route('cases.create') }}" 
               class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg shadow-xs transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create Case
            </a>
            @endcan
        </div>
    </div>

    <!-- Required Status Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        <!-- Total Cases -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Cases</span>
                <span class="p-2 rounded-lg bg-slate-100 text-slate-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </span>
            </div>
            <div class="mt-3 text-3xl font-extrabold text-slate-900">{{ number_format($totalCases) }}</div>
            <div class="mt-1 text-xs text-slate-400">All registered devices</div>
        </div>

        <!-- Planning Cases -->
        <div class="bg-white p-5 rounded-xl border border-blue-200 shadow-2xs relative overflow-hidden bg-gradient-to-br from-white to-blue-50/30">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700">Planning</span>
                <span class="p-2 rounded-lg bg-blue-100 text-blue-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </span>
            </div>
            <div class="mt-3 text-3xl font-extrabold text-blue-900">{{ number_format($planningCases) }}</div>
            <div class="mt-1 text-xs text-blue-600 font-medium">CAD design & sizing</div>
        </div>

        <!-- In Review Cases -->
        <div class="bg-white p-5 rounded-xl border border-amber-200 shadow-2xs relative overflow-hidden bg-gradient-to-br from-white to-amber-50/30">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-700">In Review</span>
                <span class="p-2 rounded-lg bg-amber-100 text-amber-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </span>
            </div>
            <div class="mt-3 text-3xl font-extrabold text-amber-900">{{ number_format($inReviewCases) }}</div>
            <div class="mt-1 text-xs text-amber-600 font-medium">Clinical peer check</div>
        </div>

        <!-- Approved Cases -->
        <div class="bg-white p-5 rounded-xl border border-teal-200 shadow-2xs relative overflow-hidden bg-gradient-to-br from-white to-teal-50/30">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-teal-700">Approved</span>
                <span class="p-2 rounded-lg bg-teal-100 text-teal-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </span>
            </div>
            <div class="mt-3 text-3xl font-extrabold text-teal-900">{{ number_format($approvedCases) }}</div>
            <div class="mt-1 text-xs text-teal-600 font-medium">Ready for surgery</div>
        </div>

        <!-- Completed Cases -->
        <div class="bg-white p-5 rounded-xl border border-emerald-200 shadow-2xs relative overflow-hidden bg-gradient-to-br from-white to-emerald-50/30">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Completed</span>
                <span class="p-2 rounded-lg bg-emerald-100 text-emerald-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </span>
            </div>
            <div class="mt-3 text-3xl font-extrabold text-emerald-900">{{ number_format($completedCases) }}</div>
            <div class="mt-1 text-xs text-emerald-600 font-medium">Successfully implanted</div>
        </div>

    </div>

    <!-- Secondary Status Summary (Draft & Cancelled) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Draft Cases (Unsubmitted)</span>
                <div class="text-xl font-bold text-slate-800 mt-0.5">{{ number_format($draftCases) }}</div>
            </div>
            <a href="{{ route('cases.index', ['status' => 'Draft']) }}" class="text-xs font-medium text-teal-600 hover:text-teal-800">Filter drafts &rarr;</a>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Cancelled / Withdrawn</span>
                <div class="text-xl font-bold text-slate-800 mt-0.5">{{ number_format($cancelledCases) }}</div>
            </div>
            <a href="{{ route('cases.index', ['status' => 'Cancelled']) }}" class="text-xs font-medium text-teal-600 hover:text-teal-800">Filter cancelled &rarr;</a>
        </div>
    </div>

    <!-- Recent Cases Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Recent Medical Implant Cases</h3>
                <p class="text-xs text-slate-500">Latest activity registered across medical centers</p>
            </div>
            <a href="{{ route('cases.index') }}" class="text-xs font-semibold text-teal-700 hover:text-teal-900">
                View All &rarr;
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">Case Number</th>
                        <th class="px-6 py-3 text-left font-semibold">Patient Ref</th>
                        <th class="px-6 py-3 text-left font-semibold">Surgeon</th>
                        <th class="px-6 py-3 text-left font-semibold">Implant Type</th>
                        <th class="px-6 py-3 text-left font-semibold">Status</th>
                        <th class="px-6 py-3 text-left font-semibold">Priority</th>
                        <th class="px-6 py-3 text-right font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($recentCases as $case)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                            <a href="{{ route('cases.show', $case) }}" class="text-teal-700 hover:underline">
                                {{ $case->case_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 font-mono text-slate-600 whitespace-nowrap">{{ $case->patient_reference }}</td>
                        <td class="px-6 py-4 text-slate-800 font-medium whitespace-nowrap">{{ $case->surgeon_name }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $case->implant_type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusStyles = [
                                    'Draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    'Planning' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'In Review' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'Approved' => 'bg-teal-50 text-teal-700 border-teal-200',
                                    'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'Cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $statusStyles[$case->status] ?? 'bg-slate-100 text-slate-800' }}">
                                {{ $case->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($case->priority === 'High')
                                <span class="text-rose-600 font-bold text-xs flex items-center gap-1">● High</span>
                            @elseif($case->priority === 'Medium')
                                <span class="text-amber-600 font-semibold text-xs flex items-center gap-1">● Medium</span>
                            @else
                                <span class="text-slate-500 font-normal text-xs flex items-center gap-1">● Low</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ route('cases.show', $case) }}" class="text-xs font-semibold text-teal-600 hover:text-teal-800">
                                Details &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                            No medical implant cases found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
