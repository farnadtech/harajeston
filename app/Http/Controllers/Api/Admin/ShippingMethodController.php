<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateShippingMethodRequest;
use App\Services\ShippingService;
use Illuminate\Http\Request;

class ShippingMethodController extends Controller
{
    public function __construct(
        private ShippingService $shippingService
    ) {}

    public function index()
    {
        $methods = $this->shippingService->getAllShippingMethods();
        
        return response()->json([
            'shipping_methods' => $methods,
        ]);
    }

    public function store(CreateShippingMethodRequest $request)
    {
        try {
            $method = $this->shippingService->createShippingMethod(
                $request->user(),
                $request->name,
                $request->description,
                $request->base_cost
            );
            
            return response()->json([
                'message' => 'روش ارسال با موفقیت ایجاد شد',
                'shipping_method' => $method,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
