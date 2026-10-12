<?php

namespace App\Http\Requests;

use App\Models\MedicalCase;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateCaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $case = $this->route('case');
        if (is_numeric($case)) {
            $case = MedicalCase::find($case);
        }

        return $case && $this->user() ? $this->user()->can('update', $case) : false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $case = $this->route('case');
        $caseId = $case instanceof MedicalCase ? $case->id : $case;

        return [
            'case_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('cases', 'case_number')->ignore($caseId),
            ],
            'patient_reference' => ['required', 'string', 'max:50'],
            'surgeon_name' => ['required', 'string', 'max:100'],
            'implant_type' => ['required', 'string', 'max:100'],
            'status' => ['required', 'string', Rule::in(MedicalCase::STATUSES)],
            'priority' => ['required', 'string', Rule::in(MedicalCase::PRIORITIES)],
            'surgery_date' => ['nullable', 'date'],
        ];
    }

    /**
     * Custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'case_number.required' => 'The Case Number is mandatory.',
            'case_number.unique' => 'This Case Number has already been assigned to another implant case.',
            'patient_reference.required' => 'The Patient Reference identifier code is required.',
            'surgeon_name.required' => 'The Operating Surgeon name is required.',
            'implant_type.required' => 'The Medical Implant Type is required.',
            'status.required' => 'Please select a valid case status.',
            'status.in' => 'The selected status is invalid. Permitted values: ' . implode(', ', MedicalCase::STATUSES),
            'priority.required' => 'Please select a priority level.',
            'priority.in' => 'The selected priority is invalid. Permitted values: ' . implode(', ', MedicalCase::PRIORITIES),
            'surgery_date.date' => 'The Surgery Date must be a valid calendar date format (YYYY-MM-DD).',
        ];
    }

    /**
     * Handle failed validation for API requests to guarantee consistent JSON shape.
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson() || $this->is('api/*')) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'The given data failed validation.',
                'errors' => $validator->errors(),
            ], 422));
        }

        parent::failedValidation($validator);
    }
}
