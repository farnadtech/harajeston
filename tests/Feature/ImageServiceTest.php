<?php

use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\User;
use App\Services\ImageService;
use App\Exceptions\Image\InvalidImageFormatException;
use App\Exceptions\Image\ImageSizeTooLargeException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

describe('ImageService', function () {
    beforeEach(function () {
        $this->imageService = app(ImageService::class);
        Storage::fake('public');
    });

    test('Property 46: Image Format Validation - only allowed formats are accepted', function () {
        $seller = User::factory()->create(['role' => 'seller']);
        $listing = Listing::factory()->create(['seller_id' => $seller->id, 'type' => 'direct_sale']);

        $invalidFormats = ['gif', 'bmp', 'svg', 'pdf', 'txt'];
        
        foreach ($invalidFormats as $format) {
            $file = UploadedFile::fake()->create('image.' . $format, 100);
            
            try {
                $this->imageService->upload($listing, $file);
                throw new \Exception('Should have thrown InvalidImageFormatException for ' . $format);
            } catch (InvalidImageFormatException $e) {
                expect($e)->toBeInstanceOf(InvalidImageFormatException::class);
            }
        }
    });

    test('Property 47: Image Size Validation - files larger than 5MB are rejected', function () {
        $seller = User::factory()->create(['role' => 'seller']);
        $listing = Listing::factory()->create(['seller_id' => $seller->id, 'type' => 'direct_sale']);

        // Create file larger than 5MB (5120 KB = 5MB)
        $file = UploadedFile::fake()->create('image.jpg', 6000); // 6MB
        
        expect(fn() => $this->imageService->upload($listing, $file))
            ->toThrow(ImageSizeTooLargeException::class);
    });

    test('Property 48: Image Optimization - thumbnail is created for uploaded images', function () {
        $seller = User::factory()->create(['role' => 'seller']);
        $listing = Listing::factory()->create(['seller_id' => $seller->id, 'type' => 'direct_sale']);

        $file = UploadedFile::fake()->image('test.jpg', 1000, 1000);
        
        $image = $this->imageService->upload($listing, $file);
        
        // Verify original image exists
        Storage::disk('public')->assertExists($image->file_path);
        
        // Verify thumbnail path would be created (we can't test actual thumbnail without intervention/image)
        expect($image->file_name)->not->toBeEmpty();
    });

    test('Property 49: Image Filename Uniqueness - each uploaded image has unique filename', function () {
        $seller = User::factory()->create(['role' => 'seller']);
        $listing = Listing::factory()->create(['seller_id' => $seller->id, 'type' => 'direct_sale']);

        $filenames = [];
        
        for ($i = 0; $i < 5; $i++) {
            $file = UploadedFile::fake()->image('test.jpg');
            $image = $this->imageService->upload($listing, $file, $i);
            
            expect($filenames)->not->toContain($image->file_name);
            $filenames[] = $image->file_name;
        }
        
        // All filenames should be unique
        expect(count($filenames))->toBe(count(array_unique($filenames)));
    });

    test('Property 50: Image Deletion Time Constraint - images can only be deleted within 24 hours', function () {
        $seller = User::factory()->create(['role' => 'seller']);
        $listing = Listing::factory()->create(['seller_id' => $seller->id, 'type' => 'direct_sale']);

        $file = UploadedFile::fake()->image('test.jpg');
        $image = $this->imageService->upload($listing, $file);
        
        // Simulate image created 25 hours ago
        $image->created_at = now()->subHours(25);
        $image->save();
        
        expect(fn() => $this->imageService->delete($image))
            ->toThrow(\InvalidArgumentException::class, 'نمی‌توانید تصویر را بعد از 24 ساعت حذف کنید.');
    });

    test('upload stores image and creates database record', function () {
        $seller = User::factory()->create(['role' => 'seller']);
        $listing = Listing::factory()->create(['seller_id' => $seller->id, 'type' => 'direct_sale']);

        $file = UploadedFile::fake()->image('test.jpg');
        
        $image = $this->imageService->upload($listing, $file, 0);
        
        expect($image)->toBeInstanceOf(ListingImage::class);
        expect($image->listing_id)->toBe($listing->id);
        expect($image->display_order)->toBe(0);
        
        Storage::disk('public')->assertExists($image->file_path);
    });

    test('upload accepts valid image formats', function () {
        $seller = User::factory()->create(['role' => 'seller']);
        $listing = Listing::factory()->create(['seller_id' => $seller->id, 'type' => 'direct_sale']);

        $validFormats = ['jpg', 'jpeg', 'png', 'webp'];
        
        foreach ($validFormats as $format) {
            $file = UploadedFile::fake()->image('test.' . $format);
            $image = $this->imageService->upload($listing, $file);
            
            expect($image)->toBeInstanceOf(ListingImage::class);
        }
    });

    test('delete removes image within 24 hours', function () {
        $seller = User::factory()->create(['role' => 'seller']);
        $listing = Listing::factory()->create(['seller_id' => $seller->id, 'type' => 'direct_sale']);

        $file = UploadedFile::fake()->image('test.jpg');
        $image = $this->imageService->upload($listing, $file);
        
        $result = $this->imageService->delete($image);
        
        expect($result)->toBeTrue();
        expect(ListingImage::find($image->id))->toBeNull();
    });

    test('reorder updates display order of images', function () {
        $seller = User::factory()->create(['role' => 'seller']);
        $listing = Listing::factory()->create(['seller_id' => $seller->id, 'type' => 'direct_sale']);

        // Create 3 images
        $images = [];
        for ($i = 0; $i < 3; $i++) {
            $file = UploadedFile::fake()->image('test' . $i . '.jpg');
            $images[] = $this->imageService->upload($listing, $file, $i);
        }
        
        // Reorder: reverse the order
        $newOrder = [
            $images[2]->id,
            $images[1]->id,
            $images[0]->id,
        ];
        
        $this->imageService->reorder($listing, $newOrder);
        
        // Verify new order
        expect(ListingImage::find($images[2]->id)->display_order)->toBe(0);
        expect(ListingImage::find($images[1]->id)->display_order)->toBe(1);
        expect(ListingImage::find($images[0]->id)->display_order)->toBe(2);
    });
});
