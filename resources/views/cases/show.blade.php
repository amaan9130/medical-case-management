@extends('layouts.app', ['title' => 'Case Details: ' . $case->case_number])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Bar Navigation & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-3 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('cases.index') }}" class="hover:underline">Cases Catalog</a>
                <span>/</span>
                <span class="text-slate-700 font-mono font-medium">{{ $case->case_number }}</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 font-mono">
                    {{ $case->case_number }}
                </h1>
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
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $statusStyles[$case->status] ?? 'bg-slate-100 text-slate-800' }}">
                    {{ $case->status }}
                </span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('cases.index') }}" 
               class="px-3.5 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-lg transition">
                &larr; Back
            </a>

            <!-- Edit Button: Shown only to Admin and Engineer -->
            @can('update', $case)
            <a href="{{ route('cases.edit', $case) }}" 
               class="px-3.5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg shadow-xs transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Case
            </a>
            @endcan

            <!-- Delete Button: Shown ONLY to Admin -->
            @can('delete', $case)
            <form action="{{ route('cases.destroy', $case) }}" method="POST" class="inline"
                  onsubmit="return confirm('WARNING: Are you sure you want to permanently delete case #{{ $case->case_number }}?');">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-3.5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Delete Case
                </button>
            </form>
            @endcan
        </div>
    </div>

    <!-- Public Holiday Alert Banner if surgery date falls on a holiday -->
    @if($holiday)
    <div class="p-4 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 flex items-start gap-3 shadow-2xs">
        <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <div>
            <span class="font-bold">Public Holiday Advisory ({{ $holiday['name'] }}):</span>
            <span>The scheduled surgery date ({{ $case->surgery_date->format('M d, Y') }}) coincides with <strong>{{ $holiday['localName'] }}</strong>. Surgical suites may operate on an emergency-only rotation.</span>
        </div>
    </div>
    @endif

    <!-- Main Specification Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-200">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700">Implant & Clinical Specification</h2>
        </div>

        <div class="p-6 divide-y divide-slate-100">
            
            <div class="py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Database Record ID</span>
                <span class="sm:col-span-2 text-sm font-mono font-medium text-slate-900">#{{ $case->id }}</span>
            </div>

            <div class="py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Case Number</span>
                <span class="sm:col-span-2 text-sm font-mono font-bold text-teal-800">{{ $case->case_number }}</span>
            </div>

            <div class="py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Patient Identifier</span>
                <div class="sm:col-span-2 flex items-center gap-2">
                    <span class="text-sm font-mono font-medium text-slate-900">{{ $case->patient_reference }}</span>
                    <span class="text-[10px] px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-medium">De-identified / Pseudo</span>
                </div>
            </div>

            <div class="py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Operating Surgeon</span>
                <span class="sm:col-span-2 text-sm font-semibold text-slate-900">{{ $case->surgeon_name }}</span>
            </div>

            <div class="py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Medical Implant Type</span>
                <span class="sm:col-span-2 text-sm font-medium text-slate-900">{{ $case->implant_type }}</span>
            </div>

            <div class="py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Clinical Priority</span>
                <span class="sm:col-span-2">
                    @if($case->priority === 'High')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">High Priority</span>
                    @elseif($case->priority === 'Medium')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">Medium Priority</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">Low Priority</span>
                    @endif
                </span>
            </div>

            <div class="py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Surgery Date</span>
                <span class="sm:col-span-2 text-sm text-slate-800">
                    @if($case->surgery_date)
                        <span class="font-medium">{{ $case->surgery_date->format('F d, Y') }}</span>
                        <span class="text-xs text-slate-500 ml-1.5">({{ $case->surgery_date->diffForHumans() }})</span>
                    @else
                        <span class="text-slate-400 italic">Not yet scheduled</span>
                    @endif
                </span>
            </div>

        </div>
    </div>

    <!-- Traceability & Audit Metadata Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-200">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700">Audit & Traceability Record</h2>
        </div>

        <div class="p-6 divide-y divide-slate-100">
            
            <div class="py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Created By User</span>
                <div class="sm:col-span-2">
                    @if($case->creator)
                        <div class="text-sm font-semibold text-slate-900">{{ $case->creator->name }}</div>
                        <div class="text-xs text-slate-500">{{ $case->creator->email }} &bull; Role: <span class="font-medium text-slate-700 capitalize">{{ $case->creator->role }}</span></div>
                    @else
                        <span class="text-sm text-slate-400">System (User ID #{{ $case->created_by }})</span>
                    @endif
                </div>
            </div>

            <div class="py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Created Timestamp</span>
                <span class="sm:col-span-2 text-sm text-slate-800 font-mono">
                    {{ $case->created_at->format('Y-m-d H:i:s T') }}
                    <span class="text-xs font-sans text-slate-400 ml-1">({{ $case->created_at->diffForHumans() }})</span>
                </span>
            </div>

            <div class="py-3.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Last Updated Timestamp</span>
                <span class="sm:col-span-2 text-sm text-slate-800 font-mono">
                    {{ $case->updated_at->format('Y-m-d H:i:s T') }}
                    <span class="text-xs font-sans text-slate-400 ml-1">({{ $case->updated_at->diffForHumans() }})</span>
                </span>
            </div>

        </div>
    </div>

</div>
@endsection
