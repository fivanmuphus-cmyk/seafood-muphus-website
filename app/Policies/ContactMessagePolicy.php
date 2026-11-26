<?php

namespace App\Policies;

use App\Models\ContactMessage;
use App\Models\User;

/** Admin biasa boleh membaca dan menandai dibaca; hanya superadmin yang boleh menghapus. Pesan tidak dibuat dari admin. */
class ContactMessagePolicy
{
    public function viewAny(User $user): bool { return true; }

    public function view(User $user, ContactMessage $model): bool { return true; }

    public function create(User $user): bool { return false; }

    public function update(User $user, ContactMessage $model): bool { return true; }

    public function delete(User $user, ContactMessage $model): bool { return $user->isSuperadmin(); }

    public function deleteAny(User $user): bool { return $user->isSuperadmin(); }

    public function restore(User $user, ContactMessage $model): bool { return $user->isSuperadmin(); }

    public function restoreAny(User $user): bool { return $user->isSuperadmin(); }

    public function forceDelete(User $user, ContactMessage $model): bool { return $user->isSuperadmin(); }

    public function forceDeleteAny(User $user): bool { return $user->isSuperadmin(); }

    public function replicate(User $user, ContactMessage $model): bool { return $user->isSuperadmin(); }

    public function reorder(User $user): bool { return true; }
}
