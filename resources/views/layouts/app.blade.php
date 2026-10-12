<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Case Manager' }} | Jajal Medical</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full flex flex-col font-sans text-slate-800 antialiased selection:bg-teal-500 selection:text-white">
    <!-- Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left brand and links -->
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center gap-2">
                        <div class="w-9 h-9 rounded-lg bg-teal-600 flex items-center justify-center text-white font-black text-lg shadow-sm">
                            JM
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-900 tracking-tight text-base leading-tight">Jajal Medical</span>
                            <span class="text-xs text-teal-700 font-medium">Implant Case Management</span>
                        </div>
                    </div>
                    @auth
                    <nav class="hidden md:ml-8 md:flex md:space-x-4 items-center">
                        <a href="{{ route('dashboard') }}" 
                           class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-teal-50 text-teal-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('cases.index') }}" 
                           class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('cases.index') || request()->routeIs('cases.show') ? 'bg-teal-50 text-teal-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Cases Catalog
                        </a>
                        @can('create', App\Models\MedicalCase::class)
                        <a href="{{ route('cases.create') }}" 
                           class="px-3 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('cases.create') ? 'bg-teal-50 text-teal-800 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            + New Case
                        </a>
                        @endcan
                    </nav>
                    @endauth
                </div>

                <!-- Right user info & logout -->
                @auth
                <div class="hidden md:flex items-center space-x-4">
                    <div class="text-right">
                        <div class="text-sm font-semibold text-slate-900">{{ Auth::user()->name }}</div>
                        <div class="flex items-center justify-end gap-1.5">
                            @if(Auth::user()->isAdmin())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">Admin</span>
                            @elseif(Auth::user()->isEngineer())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">Engineer</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">Viewer</span>
                            @endif
                            <span class="text-xs text-slate-400 font-mono">{{ Auth::user()->email }}</span>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-lg text-sm font-medium transition flex items-center gap-1.5 shadow-2xs">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Logout
                        </button>
                    </form>
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center md:hidden">
                    <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="p-2 rounded-md text-slate-500 hover:text-slate-700 hover:bg-slate-100">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                </div>
                @endauth
            </div>
        </div>

        <!-- Mobile menu dropdown -->
        @auth
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-200 bg-white px-4 pt-2 pb-4 space-y-2">
            <div class="py-2 border-b border-slate-100">
                <p class="text-sm font-semibold text-slate-800">{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</p>
                <p class="text-xs text-slate-500">{{ Auth::user()->email }}</p>
            </div>
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-slate-100">Dashboard</a>
            <a href="{{ route('cases.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-slate-100">Cases Catalog</a>
            @can('create', App\Models\MedicalCase::class)
            <a href="{{ route('cases.create') }}" class="block px-3 py-2 rounded-md text-base font-medium text-teal-700 hover:bg-teal-50">+ New Case</a>
            @endcan
            <form action="{{ route('logout') }}" method="POST" class="pt-2">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-red-50">Logout</button>
            </form>
        </div>
        @endauth
    </header>

    <!-- Global Notifications / Alerts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
        @if(session('success'))
        <div class="p-4 mb-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-start gap-3 shadow-2xs">
            <svg class="w-5 h-5 text-emerald-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div class="text-sm font-medium text-emerald-800">{{ session('success') }}</div>
        </div>
        @endif

        @if(session('error'))
        <div class="p-4 mb-4 rounded-xl bg-red-50 border border-red-200 flex items-start gap-3 shadow-2xs">
            <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div class="text-sm font-medium text-red-800">{{ session('error') }}</div>
        </div>
        @endif

        @if(session('info'))
        <div class="p-4 mb-4 rounded-xl bg-blue-50 border border-blue-200 flex items-start gap-3 shadow-2xs">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div class="text-sm font-medium text-blue-800">{{ session('info') }}</div>
        </div>
        @endif
    </div>

    <!-- Main Page Content -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-3">
            <div>&copy; 2026 Jajal Medical Devices Inc. All rights reserved. Strict Regulatory Compliance Mode.</div>
            <div class="flex gap-4">
                <span>REST API: <code class="bg-slate-100 text-slate-700 px-1 py-0.5 rounded font-mono">/api/cases</code></span>
                <span>Version 1.0.0</span>
            </div>
        </div>
    </footer>
</body>
</html>
