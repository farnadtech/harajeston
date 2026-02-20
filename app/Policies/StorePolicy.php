<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;

class StorePolicy
{
    public function update(User $user, Store $store): bool
    {
        return $user->id === $store->user_id;
    }

    public function uploadBanner(User $user, Store $store): bool
    {
        return $user->id === $store->user_id;
    }

    public function uploadLogo(User $user, Store $store): bool
    {
        return $user->id === $store->user_id;
    }
}
