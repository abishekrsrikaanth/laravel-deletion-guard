<?php

namespace WorkDoneRight\DeletionGuard\Policies;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class DeletionPolicy
{
    public function delete(User $user, Model $model): bool
    {
        if (method_exists($model, 'deletionBlockers')) {
            return empty($model->deletionBlockers());
        }

        return true;
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $user->is_admin ?? false;
    }
}
