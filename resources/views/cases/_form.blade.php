<div class="space-y-6">

    <!-- Row 1: Case Number & Patient Reference -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
            <label for="case_number" class="block text-sm font-semibold text-slate-800">
                Case Number <span class="text-rose-500">*</span>
            </label>
            <div class="mt-1">
                <input type="text" id="case_number" name="case_number"
                       value="{{ old('case_number', $case->case_number) }}"
                       placeholder="e.g. CASE-2026-001" required
                       class="w-full px-3.5 py-2.5 text-sm font-mono border {{ $errors->has('case_number') ? 'border-red-300 focus:ring-red-500 focus:border-red-500 bg-red-50/20' : 'border-slate-300 focus:ring-teal-500 focus:border-teal-500' }} rounded-lg shadow-2xs placeholder-slate-400">
            </div>
            <p class="mt-1 text-xs text-slate-500">Unique alphanumeric identification code.</p>
            @error('case_number')
                <p class="mt-1.5 text-xs text-red-600 font-semibold flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label for="patient_reference" class="block text-sm font-semibold text-slate-800">
                Patient Reference (Dummy Code) <span class="text-rose-500">*</span>
            </label>
            <div class="mt-1">
                <input type="text" id="patient_reference" name="patient_reference"
                       value="{{ old('patient_reference', $case->patient_reference) }}"
                       placeholder="e.g. PAT-9481" required
                       class="w-full px-3.5 py-2.5 text-sm font-mono border {{ $errors->has('patient_reference') ? 'border-red-300 focus:ring-red-500 focus:border-red-500 bg-red-50/20' : 'border-slate-300 focus:ring-teal-500 focus:border-teal-500' }} rounded-lg shadow-2xs placeholder-slate-400">
            </div>
            <p class="mt-1 text-xs text-slate-500">Anonymized patient identifier (no real names or PHI).</p>
            @error('patient_reference')
                <p class="mt-1.5 text-xs text-red-600 font-semibold flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    <!-- Row 2: Surgeon Name & Implant Type -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
            <label for="surgeon_name" class="block text-sm font-semibold text-slate-800">
                Operating Surgeon Name <span class="text-rose-500">*</span>
            </label>
            <div class="mt-1">
                <input type="text" id="surgeon_name" name="surgeon_name"
                       value="{{ old('surgeon_name', $case->surgeon_name) }}"
                       placeholder="e.g. Dr. Sarah Jenkins, MD" required
                       class="w-full px-3.5 py-2.5 text-sm border {{ $errors->has('surgeon_name') ? 'border-red-300 focus:ring-red-500 focus:border-red-500 bg-red-50/20' : 'border-slate-300 focus:ring-teal-500 focus:border-teal-500' }} rounded-lg shadow-2xs placeholder-slate-400">
            </div>
            @error('surgeon_name')
                <p class="mt-1.5 text-xs text-red-600 font-semibold flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label for="implant_type" class="block text-sm font-semibold text-slate-800">
                Medical Implant Type <span class="text-rose-500">*</span>
            </label>
            <div class="mt-1">
                <input type="text" id="implant_type" name="implant_type" list="implant-presets"
                       value="{{ old('implant_type', $case->implant_type) }}"
                       placeholder="e.g. Titanium Cranial Reconstruction Plate" required
                       class="w-full px-3.5 py-2.5 text-sm border {{ $errors->has('implant_type') ? 'border-red-300 focus:ring-red-500 focus:border-red-500 bg-red-50/20' : 'border-slate-300 focus:ring-teal-500 focus:border-teal-500' }} rounded-lg shadow-2xs placeholder-slate-400">
                <datalist id="implant-presets">
                    <option value="Titanium Cranial Reconstruction Plate">
                    <option value="Acetabular Hip Revision Cup">
                    <option value="Cervical Spine Interbody Cage">
                    <option value="Bicondylar Femoral Knee Implant">
                    <option value="Custom 3D-Printed Mandibular Scaffold">
                    <option value="Porous Titanium Trabecular Wedge">
                    <option value="Anatomic Shoulder Glenoid Component">
                    <option value="Bioabsorbable Interference Screw">
                </datalist>
            </div>
            @error('implant_type')
                <p class="mt-1.5 text-xs text-red-600 font-semibold flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    <!-- Row 3: Status & Priority -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
            <label for="status" class="block text-sm font-semibold text-slate-800">
                Case Lifecycle Status <span class="text-rose-500">*</span>
            </label>
            <div class="mt-1">
                <select id="status" name="status" required
                        class="w-full px-3.5 py-2.5 text-sm border {{ $errors->has('status') ? 'border-red-300 focus:ring-red-500 focus:border-red-500 bg-red-50/20' : 'border-slate-300 focus:ring-teal-500 focus:border-teal-500' }} rounded-lg shadow-2xs bg-white">
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" {{ old('status', $case->status ?? 'Draft') === $st ? 'selected' : '' }}>
                            {{ $st }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('status')
                <p class="mt-1.5 text-xs text-red-600 font-semibold flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label for="priority" class="block text-sm font-semibold text-slate-800">
                Priority Level <span class="text-rose-500">*</span>
            </label>
            <div class="mt-1">
                <select id="priority" name="priority" required
                        class="w-full px-3.5 py-2.5 text-sm border {{ $errors->has('priority') ? 'border-red-300 focus:ring-red-500 focus:border-red-500 bg-red-50/20' : 'border-slate-300 focus:ring-teal-500 focus:border-teal-500' }} rounded-lg shadow-2xs bg-white">
                    @foreach($priorities as $pr)
                        <option value="{{ $pr }}" {{ old('priority', $case->priority ?? 'Medium') === $pr ? 'selected' : '' }}>
                            {{ $pr }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('priority')
                <p class="mt-1.5 text-xs text-red-600 font-semibold flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    <!-- Row 4: Surgery Date & Public Holiday Integration -->
    <div>
        <label for="surgery_date" class="block text-sm font-semibold text-slate-800">
            Scheduled Surgery Date <span class="text-slate-400 font-normal">(Optional)</span>
        </label>
        <div class="mt-1">
            <input type="date" id="surgery_date" name="surgery_date"
                   value="{{ old('surgery_date', $case->surgery_date ? $case->surgery_date->format('Y-m-d') : '') }}"
                   class="w-full sm:w-1/2 px-3.5 py-2.5 text-sm border {{ $errors->has('surgery_date') ? 'border-red-300 focus:ring-red-500 focus:border-red-500 bg-red-50/20' : 'border-slate-300 focus:ring-teal-500 focus:border-teal-500' }} rounded-lg shadow-2xs">
        </div>
        @error('surgery_date')
            <p class="mt-1.5 text-xs text-red-600 font-semibold flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                {{ $message }}
            </p>
        @enderror

        <!-- Public Holiday Intelligence Banner (Bonus Feature: Nager.Date API) -->
        @if(!empty($upcomingHolidays))
        <div class="mt-3 p-3 rounded-lg bg-teal-50/60 border border-teal-200/70 text-xs text-teal-900 flex items-start gap-2 max-w-2xl">
            <svg class="w-4 h-4 text-teal-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <div>
                <span class="font-bold">Public Holiday Advisory (Nager.Date API):</span>
                <span>Hospital surgical theater availability may be restricted on national holidays. Check upcoming calendar dates before locking surgery schedule.</span>
            </div>
        </div>
        @endif
    </div>

</div>
