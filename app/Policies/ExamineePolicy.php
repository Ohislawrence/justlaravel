<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Examinee;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamineePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasRole('admin') || $user->hasRole('examiner');
    }

    public function view(User $user, Examinee $examinee)
    {
        return $user->organization_id === $examinee->organization_id;
    }

    public function create(User $user)
    {
        return $user->hasRole('admin') || $user->hasRole('examiner');
    }

    public function update(User $user, Examinee $examinee)
    {
        return $user->organization_id === $examinee->organization_id;
    }

    public function delete(User $user, Examinee $examinee)
    {
        return $user->organization_id === $examinee->organization_id;
    }
}
