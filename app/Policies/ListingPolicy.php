<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;

class ListingPolicy
{
    /**
     * Determine if the user can view the listing
     */
    public function view(?User $user, Listing $listing): bool
    {
        // Everyone can view active listings
        if ($listing->status === 'active') {
            return true;
        }

        // Owner can view their own listings
        if ($user && $listing->seller_id === $user->id) {
            return true;
        }

        // Admins can view all listings
        if ($user && $user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create listings
     */
    public function create(User $user): bool
    {
        return $user->canSell();
    }

    /**
     * Determine if the user can update the listing
     */
    public function update(User $user, Listing $listing): bool
    {
        // Owner can update their own listings
        if ($listing->seller_id === $user->id) {
            return true;
        }

        // Admins can update all listings
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete the listing
     */
    public function delete(User $user, Listing $listing): bool
    {
        // Owner can delete their own listings if not active
        if ($listing->seller_id === $user->id && $listing->status !== 'active') {
            return true;
        }

        // Admins can delete all listings
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }
}
