<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadImageRequest;
use App\Services\ImageService;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function __construct(
        private ImageService $imageService
    ) {}

    public function upload(UploadImageRequest $request)
    {
        try {
            $images = [];
            
            foreach ($request->file('images') as $image) {
                $path = $this->imageService->upload($image, 'listings');
                $images[] = [
                    'path' => $path,
                    'url' => asset('storage/' . $path),
                ];
            }
            
            return response()->json([
                'message' => 'تصاویر با موفقیت آپلود شدند',
                'images' => $images,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
