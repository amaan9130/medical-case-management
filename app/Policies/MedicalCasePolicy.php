<?php

namespace App\Policies;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedicalCasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any medical cases.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'engineer', 'viewer']);
    }

    /**
     * Determine whether the user can view the specific case.
     */
    public function view(User $user, MedicalCase $medicalCase): bool
    {
        return in_array($user->role, ['admin', 'engineer', 'viewer']);
    }

    /**
     * Determine whether the user can create medical cases.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'engineer']);
    }

    /**
     * Determine whether the user can update the medical case.
     */
    public function update(User $user, MedicalCase $medicalCase): bool
    {
        return in_array($user->role, ['admin', 'engineer']);
    }

    /**
     * Determine whether the user can delete the medical case.
     * Strictly Admin only. Viewers and Engineers receive 403 Forbidden.
     */
    public function delete(User $user, MedicalCase $medicalCase): bool
    {
        return $user->isAdmin();
    }
}
