<?php

namespace App\Policies;

use App\Models\Branch;
use App\Models\User;

/** Admin biasa boleh melihat, menambah, dan mengubah; hanya superadmin yang boleh menghapus. */
class BranchPolicy
{
    public function viewAny(User $user): bool { return true; }

    public function view(User $user, Branch $model): bool { return true; }

    public function create(User $user): bool { return true; }

    public function update(User $user, Branch $model): bool { return true; }

    public function delete(User $user, Branch $model): bool { return $user->isSuperadmin(); }

    public function deleteAny(User $user): bool { return $user->isSuperadmin(); }

    public function restore(User $user, Branch $model): bool { return $user->isSuperadmin(); }

    public function restoreAny(User $user): bool { return $user->isSuperadmin(); }

    public function forceDelete(User $user, Branch $model): bool { return $user->isSuperadmin(); }

    public function forceDeleteAny(User $user): bool { return $user->isSuperadmin(); }

    public function replicate(User $user, Branch $model): bool { return $user->isSuperadmin(); }

    public function reorder(User $user): bool { return true; }
}
