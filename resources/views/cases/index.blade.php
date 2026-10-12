@extends('layouts.app', ['title' => 'Medical Cases Catalog'])

@section('content')
<div class="space-y-6">

    <!-- Title and Actions Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Medical Implant Cases</h1>
            <p class="text-sm text-slate-500 mt-1">Search, track, and manage all patient implant devices.</p>
        </div>
        <div>
            @can('create', App\Models\MedicalCase::class)
            <a href="{{ route('cases.create') }}" 
               class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg shadow-xs transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                + Register New Case
            </a>
            @endcan
        </div>
    </div>

    <!-- Search, Filter & Sort Form -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('cases.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            
            <!-- Search by Case Number or Surgeon -->
            <div class="lg:col-span-4">
                <label for="search" class="block text-xs font-semibold text-slate-700 mb-1">Search</label>
                <div class="relative">
                    <input type="text" id="search" name="search" value="{{ $search }}"
                           placeholder="Case # (e.g. CASE-1001) or Surgeon..."
                           class="w-full pl-9 pr-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 placeholder-slate-400">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Filter by Status -->
            <div class="lg:col-span-3">
                <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">Status Filter</label>
                <select id="status" name="status"
                        class="w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 bg-white">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" {{ $status === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter by Priority (Bonus) -->
            <div class="lg:col-span-2">
                <label for="priority" class="block text-xs font-semibold text-slate-700 mb-1">Priority</label>
                <select id="priority" name="priority"
                        class="w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 bg-white">
                    <option value="">All Priorities</option>
                    @foreach($priorities as $pr)
                        <option value="{{ $pr }}" {{ $priority === $pr ? 'selected' : '' }}>{{ $pr }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Hidden Sort inputs so filters preserve current sort -->
            <input type="hidden" name="sort_by" value="{{ $sortBy }}">
            <input type="hidden" name="sort_dir" value="{{ $sortDir }}">

            <!-- Actions -->
            <div class="lg:col-span-3 flex gap-2">
                <button type="submit" 
                        class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-lg shadow-xs transition">
                    Apply Filter
                </button>
                @if($search || $status || $priority)
                <a href="{{ route('cases.index') }}" 
                   class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition" title="Clear all filters">
                    Reset
                </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Cases Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider">
                    <tr>
                        @php
                            if (!function_exists('sortCaseUrl')) {
                                function sortCaseUrl($col, $currentCol, $currentDir) {
                                    $newDir = ($currentCol === $col && $currentDir === 'asc') ? 'desc' : 'asc';
                                    return request()->fullUrlWithQuery(['sort_by' => $col, 'sort_dir' => $newDir]);
                                }
                            }
                        @endphp
                        <th class="px-6 py-3.5 text-left font-semibold">
                            <a href="{{ sortCaseUrl('case_number', $sortBy, $sortDir) }}" class="flex items-center gap-1 hover:text-slate-900 group">
                                Case #
                                @if($sortBy === 'case_number')
                                    <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 group-hover:text-slate-400">↕</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3.5 text-left font-semibold">Patient Ref</th>
                        <th class="px-6 py-3.5 text-left font-semibold">
                            <a href="{{ sortCaseUrl('surgeon_name', $sortBy, $sortDir) }}" class="flex items-center gap-1 hover:text-slate-900 group">
                                Surgeon
                                @if($sortBy === 'surgeon_name')
                                    <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 group-hover:text-slate-400">↕</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3.5 text-left font-semibold">Implant Type</th>
                        <th class="px-6 py-3.5 text-left font-semibold">
                            <a href="{{ sortCaseUrl('status', $sortBy, $sortDir) }}" class="flex items-center gap-1 hover:text-slate-900 group">
                                Status
                                @if($sortBy === 'status')
                                    <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 group-hover:text-slate-400">↕</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3.5 text-left font-semibold">
                            <a href="{{ sortCaseUrl('priority', $sortBy, $sortDir) }}" class="flex items-center gap-1 hover:text-slate-900 group">
                                Priority
                                @if($sortBy === 'priority')
                                    <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 group-hover:text-slate-400">↕</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3.5 text-left font-semibold">
                            <a href="{{ sortCaseUrl('surgery_date', $sortBy, $sortDir) }}" class="flex items-center gap-1 hover:text-slate-900 group">
                                Surgery Date
                                @if($sortBy === 'surgery_date')
                                    <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 group-hover:text-slate-400">↕</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3.5 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($cases as $case)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-6 py-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                            <a href="{{ route('cases.show', $case) }}" class="text-teal-700 hover:text-teal-900 hover:underline">
                                {{ $case->case_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-slate-600 whitespace-nowrap">
                            {{ $case->patient_reference }}
                        </td>
                        <td class="px-6 py-4 text-slate-800 font-medium whitespace-nowrap">
                            {{ $case->surgeon_name }}
                        </td>
                        <td class="px-6 py-4 text-slate-600 max-w-xs truncate" title="{{ $case->implant_type }}">
                            {{ $case->implant_type }}
                        </td>
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
                                <span class="text-rose-600 font-bold text-xs">High</span>
                            @elseif($case->priority === 'Medium')
                                <span class="text-amber-600 font-semibold text-xs">Medium</span>
                            @else
                                <span class="text-slate-500 font-normal text-xs">Low</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600 whitespace-nowrap text-xs">
                            {{ $case->surgery_date ? $case->surgery_date->format('M d, Y') : '—' }}
                        </td>
                        
                        <!-- Role-Guarded Actions Column -->
                        <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                            <!-- View Button: Shown to all authenticated users -->
                            <a href="{{ route('cases.show', $case) }}" 
                               class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
                               title="View Details">
                                View
                            </a>

                            <!-- Edit Button: Shown only to Admin and Engineer -->
                            @can('update', $case)
                            <a href="{{ route('cases.edit', $case) }}" 
                               class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-teal-50 hover:bg-teal-100 text-teal-800 transition"
                               title="Edit Case">
                                Edit
                            </a>
                            @endcan

                            <!-- Delete Button: Shown ONLY to Admin -->
                            @can('delete', $case)
                            <form action="{{ route('cases.destroy', $case) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Are you sure you want to permanently delete case #{{ $case->case_number }}? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-red-50 hover:bg-red-100 text-red-700 transition"
                                        title="Delete Case">
                                    Delete
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-sm font-medium text-slate-600">No matching medical cases found</p>
                                <p class="text-xs text-slate-400 mt-1">Try modifying your search criteria or filter tags.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar (10 per page) -->
        @if($cases->hasPages())
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            {{ $cases->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
