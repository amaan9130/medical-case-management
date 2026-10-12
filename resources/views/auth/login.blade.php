@extends('layouts.app', ['title' => 'Sign In'])

@section('content')
<div class="min-h-[70vh] flex flex-col justify-center py-8 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="mx-auto w-14 h-14 rounded-2xl bg-teal-600 flex items-center justify-center text-white font-extrabold text-2xl shadow-md">
            JM
        </div>
        <h2 class="mt-4 text-center text-2xl font-bold tracking-tight text-slate-900">
            Sign in to Case Management
        </h2>
        <p class="mt-1 text-center text-sm text-slate-500">
            Authorized personnel portal for patient implant workflows
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-6 shadow-sm border border-slate-200 rounded-2xl sm:px-10">
            
            @if ($errors->has('email') && !old('email'))
                <div class="mb-5 p-3 rounded-lg bg-red-50 border border-red-200 text-xs font-medium text-red-700">
                    {{ $errors->first('email') }}
                </div>
            @endif

            <form class="space-y-5" action="{{ route('login') }}" method="POST">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">
                        Work Email Address
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="{{ old('email') }}"
                               class="appearance-none block w-full px-3 py-2 border {{ $errors->has('email') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-slate-300 focus:ring-teal-500 focus:border-teal-500' }} rounded-lg shadow-2xs placeholder-slate-400 focus:outline-none sm:text-sm"
                               placeholder="user@jajalmedical.com">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">
                        Password
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="appearance-none block w-full px-3 py-2 border {{ $errors->has('password') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-slate-300 focus:ring-teal-500 focus:border-teal-500' }} rounded-lg shadow-2xs placeholder-slate-400 focus:outline-none sm:text-sm"
                               placeholder="••••••••••••">
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-slate-300 rounded">
                        <label for="remember" class="ml-2 block text-xs text-slate-700">
                            Remember my session
                        </label>
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-xs text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition">
                        Sign In
                    </button>
                </div>
            </form>

            <!-- Quick Demo Credentials Selector for Reviewers -->
            <div class="mt-6 pt-6 border-t border-slate-100">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 text-center mb-3">
                    Reviewer Demo Login Presets
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" 
                            onclick="fillCredentials('admin@jajalmedical.com', 'password123')"
                            class="p-2 text-center rounded-lg border border-purple-200 bg-purple-50 hover:bg-purple-100 text-purple-900 transition text-xs font-semibold">
                        Admin
                        <span class="block text-[10px] font-normal text-purple-600">Full Access</span>
                    </button>
                    <button type="button" 
                            onclick="fillCredentials('engineer@jajalmedical.com', 'password123')"
                            class="p-2 text-center rounded-lg border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-900 transition text-xs font-semibold">
                        Engineer
                        <span class="block text-[10px] font-normal text-blue-600">No Delete</span>
                    </button>
                    <button type="button" 
                            onclick="fillCredentials('viewer@jajalmedical.com', 'password123')"
                            class="p-2 text-center rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-800 transition text-xs font-semibold">
                        Viewer
                        <span class="block text-[10px] font-normal text-slate-500">Read Only</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function fillCredentials(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
}
</script>
@endsection
