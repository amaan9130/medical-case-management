<?php

namespace Database\Factories;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalCase>
 */
class MedicalCaseFactory extends Factory
{
    protected $model = MedicalCase::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $implantTypes = [
            'Titanium Cranial Reconstruction Plate',
            'Acetabular Hip Revision Cup',
            'Cervical Spine Interbody Cage',
            'Bicondylar Femoral Knee Implant',
            'Custom 3D-Printed Mandibular Scaffold',
            'Porous Titanium Trabecular Wedge',
            'Anatomic Shoulder Glenoid Component',
            'Bioabsorbable Interference Fixation Screw',
            'Thoracolumbar Posterior Rod & Screw System',
            'Zygomatic Custom Facial Prosthesis',
        ];

        $surgeons = [
            'Dr. Sarah Jenkins, MD',
            'Dr. Marcus Thorne, FRCS',
            'Dr. Priya Patel, MS (Ortho)',
            'Dr. James Wilson, MD',
            'Dr. Elena Rostova, PhD',
            'Dr. Robert Chen, MD',
            'Dr. Amina Al-Mansoor, MD',
        ];

        return [
            'case_number' => 'CASE-' . fake()->unique()->numerify('####'),
            'patient_reference' => 'PAT-' . fake()->numerify('####'),
            'surgeon_name' => fake()->randomElement($surgeons),
            'implant_type' => fake()->randomElement($implantTypes),
            'status' => fake()->randomElement(MedicalCase::STATUSES),
            'priority' => fake()->randomElement(MedicalCase::PRIORITIES),
            'surgery_date' => fake()->boolean(80) ? fake()->dateTimeBetween('+1 days', '+60 days')->format('Y-m-d') : null,
            'created_by' => User::factory(),
        ];
    }
}
