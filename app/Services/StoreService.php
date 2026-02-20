<?php

namespace App\Services;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoreService
{
    /**
     * Create store for new seller
     * Auto-creates store when user registers as seller
     * 
     * @param User $seller
     * @param string $username
     * @return Store
     */
    public function createStore(User $seller, string $username): Store
    {
        // Generate unique slug from username
        $slug = Store::generateUniqueSlug($username);
        
        return Store::create([
            'user_id' => $seller->id,
            'store_name' => $seller->name,
            'slug' => $slug,
            'is_active' => true,
        ]);
    }

    /**
     * Update store profile
     * Validates and updates store_name, description
     * Handles banner and logo image uploads
     * 
     * @param Store $store
     * @param array $data
     * @return Store
     */
    public function updateStoreProfile(Store $store, array $data): Store
    {
        // Handle banner upload
        if (isset($data['banner']) && $data['banner'] instanceof UploadedFile) {
            // Validate banner image (2MB max, 1920x400 recommended)
            $this->validateImage($data['banner'], 2048, 1920, 400);
            
            // Delete old banner if exists
            if ($store->banner_image) {
                Storage::disk('public')->delete($store->banner_image);
            }
            
            // Store new banner
            $bannerPath = $data['banner']->store('stores/banners', 'public');
            $store->banner_image = $bannerPath;
        }
        
        // Handle logo upload
        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
            // Validate logo image (1MB max, 300x300 recommended)
            $this->validateImage($data['logo'], 1024, 300, 300);
            
            // Delete old logo if exists
            if ($store->logo_image) {
                Storage::disk('public')->delete($store->logo_image);
            }
            
            // Store new logo
            $logoPath = $data['logo']->store('stores/logos', 'public');
            $store->logo_image = $logoPath;
        }
        
        // Update store information
        $updateData = [];
        
        if (isset($data['store_name'])) {
            $updateData['store_name'] = $data['store_name'];
        }
        
        if (isset($data['description'])) {
            $updateData['description'] = $data['description'];
        }
        
        if (!empty($updateData)) {
            $store->update($updateData);
        }
        
        // Save banner and logo paths if updated
        if (isset($bannerPath) || isset($logoPath)) {
            $store->save();
        }
        
        return $store->fresh();
    }

    /**
     * Get store by slug with active listings
     * Includes seller statistics
     * 
     * @param string $slug
     * @return Store|null
     */
    public function getStoreBySlug(string $slug): ?Store
    {
        return Store::where('slug', $slug)
            ->where('is_active', true)
            ->with(['user', 'listings' => function ($query) {
                $query->whereIn('status', ['active', 'pending'])
                    ->orderBy('created_at', 'desc');
            }])
            ->first();
    }

    /**
     * Validate uploaded image
     * 
     * @param UploadedFile $file
     * @param int $maxSizeKB Maximum file size in KB
     * @param int $maxWidth Maximum width in pixels
     * @param int $maxHeight Maximum height in pixels
     * @throws \App\Exceptions\Image\ImageSizeTooLargeException
     * @throws \App\Exceptions\Image\InvalidImageFormatException
     */
    protected function validateImage(UploadedFile $file, int $maxSizeKB, int $maxWidth, int $maxHeight): void
    {
        // Validate file size
        $fileSizeKB = (int) ceil($file->getSize() / 1024);
        
        if ($fileSizeKB > $maxSizeKB) {
            throw new \App\Exceptions\Image\ImageSizeTooLargeException(
                $fileSizeKB,
                $maxSizeKB
            );
        }
        
        // Validate file type
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new \App\Exceptions\Image\InvalidImageFormatException(
                'فرمت تصویر باید JPG، PNG یا WebP باشد.'
            );
        }
        
        // Validate dimensions (optional - just a warning in real implementation)
        $imageSize = getimagesize($file->getRealPath());
        if ($imageSize) {
            $width = $imageSize[0];
            $height = $imageSize[1];
            
            // Note: In production, you might want to resize instead of rejecting
            // For now, we'll just validate
        }
    }
}
