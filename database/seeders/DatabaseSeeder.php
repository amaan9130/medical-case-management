<?php

namespace Database\Seeders;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make(env('SEED_DEFAULT_PASSWORD', 'password123'));

        // Seed Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@jajalmedical.com'],
            [
                'name' => 'System Administrator',
                'password' => $defaultPassword,
                'role' => 'admin',
            ]
        );

        // Seed Viewer user
        $viewer = User::firstOrCreate(
            ['email' => 'viewer@jajalmedical.com'],
            [
                'name' => 'Clinical Viewer',
                'password' => $defaultPassword,
                'role' => 'viewer',
            ]
        );

        // Seed Engineer user (Bonus role)
        $engineer = User::firstOrCreate(
            ['email' => 'engineer@jajalmedical.com'],
            [
                'name' => 'Biomedical Engineer',
                'password' => $defaultPassword,
                'role' => 'engineer',
            ]
        );

        // Curated set of realistic medical implant cases
        $seedCases = [
            [
                'case_number' => 'CASE-1001',
                'patient_reference' => 'PAT-8401',
                'surgeon_name' => 'Dr. Sarah Jenkins, MD',
                'implant_type' => 'Titanium Cranial Reconstruction Plate',
                'status' => MedicalCase::STATUS_PLANNING,
                'priority' => MedicalCase::PRIORITY_HIGH,
                'surgery_date' => now()->addDays(7)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1002',
                'patient_reference' => 'PAT-8402',
                'surgeon_name' => 'Dr. Marcus Thorne, FRCS',
                'implant_type' => 'Acetabular Hip Revision Cup',
                'status' => MedicalCase::STATUS_IN_REVIEW,
                'priority' => MedicalCase::PRIORITY_MEDIUM,
                'surgery_date' => now()->addDays(12)->format('Y-m-d'),
                'created_by' => $engineer->id,
            ],
            [
                'case_number' => 'CASE-1003',
                'patient_reference' => 'PAT-8403',
                'surgeon_name' => 'Dr. Priya Patel, MS (Ortho)',
                'implant_type' => 'Cervical Spine Interbody Cage',
                'status' => MedicalCase::STATUS_APPROVED,
                'priority' => MedicalCase::PRIORITY_HIGH,
                'surgery_date' => now()->addDays(15)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1004',
                'patient_reference' => 'PAT-8404',
                'surgeon_name' => 'Dr. James Wilson, MD',
                'implant_type' => 'Bicondylar Femoral Knee Implant',
                'status' => MedicalCase::STATUS_COMPLETED,
                'priority' => MedicalCase::PRIORITY_LOW,
                'surgery_date' => now()->subDays(5)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1005',
                'patient_reference' => 'PAT-8405',
                'surgeon_name' => 'Dr. Elena Rostova, PhD',
                'implant_type' => 'Custom 3D-Printed Mandibular Scaffold',
                'status' => MedicalCase::STATUS_PLANNING,
                'priority' => MedicalCase::PRIORITY_HIGH,
                'surgery_date' => now()->addDays(20)->format('Y-m-d'),
                'created_by' => $engineer->id,
            ],
            [
                'case_number' => 'CASE-1006',
                'patient_reference' => 'PAT-8406',
                'surgeon_name' => 'Dr. Robert Chen, MD',
                'implant_type' => 'Porous Titanium Trabecular Wedge',
                'status' => MedicalCase::STATUS_IN_REVIEW,
                'priority' => MedicalCase::PRIORITY_MEDIUM,
                'surgery_date' => now()->addDays(9)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1007',
                'patient_reference' => 'PAT-8407',
                'surgeon_name' => 'Dr. Amina Al-Mansoor, MD',
                'implant_type' => 'Anatomic Shoulder Glenoid Component',
                'status' => MedicalCase::STATUS_APPROVED,
                'priority' => MedicalCase::PRIORITY_LOW,
                'surgery_date' => now()->addDays(18)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1008',
                'patient_reference' => 'PAT-8408',
                'surgeon_name' => 'Dr. Sarah Jenkins, MD',
                'implant_type' => 'Bioabsorbable Interference Screw',
                'status' => MedicalCase::STATUS_DRAFT,
                'priority' => MedicalCase::PRIORITY_LOW,
                'surgery_date' => null,
                'created_by' => $engineer->id,
            ],
            [
                'case_number' => 'CASE-1009',
                'patient_reference' => 'PAT-8409',
                'surgeon_name' => 'Dr. Marcus Thorne, FRCS',
                'implant_type' => 'Thoracolumbar Posterior Rod System',
                'status' => MedicalCase::STATUS_COMPLETED,
                'priority' => MedicalCase::PRIORITY_HIGH,
                'surgery_date' => now()->subDays(14)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1010',
                'patient_reference' => 'PAT-8410',
                'surgeon_name' => 'Dr. Priya Patel, MS (Ortho)',
                'implant_type' => 'Zygomatic Custom Facial Prosthesis',
                'status' => MedicalCase::STATUS_CANCELLED,
                'priority' => MedicalCase::PRIORITY_MEDIUM,
                'surgery_date' => null,
                'created_by' => $admin->id,
            ],
        ];

        foreach ($seedCases as $caseData) {
            MedicalCase::firstOrCreate(
                ['case_number' => $caseData['case_number']],
                $caseData
            );
        }

        // Generate 15 additional cases using factory to exceed the 20 minimum requirement
        MedicalCase::factory()
            ->count(15)
            ->recycle([$admin, $engineer])
            ->create();
    }
}
