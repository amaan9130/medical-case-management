@extends('layouts.app', ['title' => 'Register New Case'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('cases.index') }}" class="hover:underline">Cases</a>
                <span>/</span>
                <span class="text-slate-700 font-medium">New Case</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Register New Implant Case</h1>
        </div>
        <a href="{{ route('cases.index') }}" 
           class="px-3.5 py-1.5 border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-medium rounded-lg transition">
            Cancel
        </a>
    </div>

    <!-- Form Container -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-2xs">
        <form action="{{ route('cases.store') }}" method="POST">
            @csrf

            @include('cases._form')

            <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('cases.index') }}" 
                   class="px-4 py-2 border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-lg transition">
                    Discard
                </a>
                <button type="submit" 
                        class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg shadow-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Save & Create Case
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
