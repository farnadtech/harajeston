<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\ListingImage;
use App\Exceptions\Image\InvalidImageFormatException;
use App\Exceptions\Image\ImageSizeTooLargeException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * Maximum file size in bytes (5MB)
     */
    const MAX_FILE_SIZE = 5 * 1024 * 1024;

    /**
     * Allowed image formats
     */
    const ALLOWED_FORMATS = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Upload and store listing image
     */
    public function upload(Listing $listing, UploadedFile $file, int $displayOrder = 0): ListingImage
    {
        // Validate format
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_FORMATS)) {
            throw new InvalidImageFormatException($extension);
        }

        // Validate size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new ImageSizeTooLargeException($file->getSize(), self::MAX_FILE_SIZE);
        }

        // Generate unique filename
        $filename = $this->generateUniqueFilename($extension);

        // Store original image
        $path = $file->storeAs('listings/' . $listing->id, $filename, 'public');

        // Create thumbnail (simplified - just copy the file for now)
        $this->createThumbnail($file, $listing->id, $filename);

        // Create database record
        return ListingImage::create([
            'listing_id' => $listing->id,
            'file_path' => $path,
            'file_name' => $filename,
            'display_order' => $displayOrder,
        ]);
    }

    /**
     * Generate unique filename
     */
    protected function generateUniqueFilename(string $extension): string
    {
        return uniqid() . '_' . time() . '.' . $extension;
    }

    /**
     * Create thumbnail for image (simplified version without intervention/image)
     */
    protected function createThumbnail(UploadedFile $file, int $listingId, string $filename): void
    {
        $thumbnailPath = 'listings/' . $listingId . '/thumbnails';
        
        // Store a copy as thumbnail (in production, you would use intervention/image to resize)
        $file->storeAs($thumbnailPath, $filename, 'public');
    }

    /**
     * Delete image
     */
    public function delete(ListingImage $image): bool
    {
        // Check time constraint (can only delete within 24 hours of upload)
        $twentyFourHoursAgo = now()->subHours(24);
        if ($image->created_at->lt($twentyFourHoursAgo)) {
            throw new \InvalidArgumentException('نمی‌توانید تصویر را بعد از 24 ساعت حذف کنید.');
        }

        // Delete files from storage
        Storage::disk('public')->delete($image->file_path);
        
        // Delete thumbnail
        $thumbnailPath = 'listings/' . $image->listing_id . '/thumbnails/' . $image->file_name;
        Storage::disk('public')->delete($thumbnailPath);

        // Delete database record
        return $image->delete();
    }

    /**
     * Reorder images
     */
    public function reorder(Listing $listing, array $imageIds): void
    {
        foreach ($imageIds as $order => $imageId) {
            ListingImage::where('id', $imageId)
                ->where('listing_id', $listing->id)
                ->update(['display_order' => $order]);
        }
    }
}
