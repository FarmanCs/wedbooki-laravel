<?php

namespace App\Src\Services\Vendor;

use App\Models\services\Service;
use App\Models\Vendor;
use App\Models\Vendor\Business;
use App\Models\Vendor\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class VendorPackageService
{
    public function createPackage($businessId, array $data): JsonResponse
    {
        $business = Business::find($businessId);

        if (!$business) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        $validator = Validator::make($data, [
            'name' => 'required|string',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'is_popular' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $discountPercentage = 0;
        if (isset($data['discount']) && $data['discount']) {
            $discountPercentage = (($data['price'] - $data['discount']) / $data['price']) * 100;
        }

        $package = Package::create([
            'business_id' => $business->id,
            'name' => $data['name'],
            'price' => $data['price'],
            'discount' => $data['discount'] ?? $data['price'],
            'discount_percentage' => $discountPercentage,
            'description' => $data['description'] ?? null,
            'features' => $data['features'] ?? [],
            'is_popular' => $data['is_popular'] ?? true,
        ]);

        // Add to business packages
        $packages = $business->packages ?? [];
        $packages[] = $package->id;
        $business->packages = $packages;
        $business->save();

        return response()->json([
            'message' => 'Package created',
            'newPackage' => $package
        ], 200);
    }

    public function updatePackage($id, array $data): JsonResponse
    {
        if (!isset($data['id'])) {
            return response()->json(['message' => 'Package ID (_id) is required'], 400);
        }

        $package = Package::find($data['id']);

        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }

        unset($data['_id']);

        // Handle discount calculation
        if (isset($data['price'])) {
            if (!isset($data['discount'])) {
                $data['discount'] = $data['price'];
                $data['discount_percentage'] = 0;
            } else {
                $data['discount_percentage'] = (($data['price'] - $data['discount']) / $data['price']) * 100;
            }
        } elseif (isset($data['discount'])) {
            $data['discount_percentage'] = (($package->price - $data['discount']) / $package->price) * 100;
        }

        $package->update($data);

        return response()->json([
            'message' => 'Package updated',
            'updatedPackage' => $package->fresh()
        ], 200);
    }

    public function deletePackage($businessId, array $data): JsonResponse
    {
        $package = Package::find($data['packageId']);

        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }

        // Remove from business packages
        $business = Business::find($businessId);
        $packages = $business->packages ?? [];
        $packages = array_filter($packages, fn($id) => $id !== $data['packageId']);
        $business->packages = array_values($packages);
        $business->save();

        $package->delete();

        return response()->json(['message' => 'Package deleted'], 200);
    }

    public function getAllPackages($businessId): JsonResponse
    {
        $business = Business::with('packagesRelation')->find($businessId);

        if (!$business) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        return response()->json(['packages' => $business->packagesRelation], 200);
    }

    public function createService($userId, array $data): JsonResponse
    {
        $vendor = Vendor::find($userId);

        if (!$vendor || $vendor->role !== 'vendor') {
            return response()->json(['message' => 'Unauthorized or user not found.'], 403);
        }

        $validator = Validator::make($data, [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $service = Service::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'category' => $data['category'],
            'vendor_id' => $vendor->id,
        ]);

        // Add to vendor services
        $services = $vendor->services ?? [];
        $services[] = $service->id;
        $vendor->services = $services;
        $vendor->save();

        return response()->json(['message' => 'Service created successfully'], 201);
    }

    public function updateService($userId, array $data): JsonResponse
    {
        // Implementation for updating service
    }

    public function deleteService($vendorId, array $data): JsonResponse
    {
        $service = Service::where('id', $data['serviceId'])
            ->where('vendor_id', $vendorId)
            ->first();

        if (!$service) {
            return response()->json(['message' => 'Service not found for this vendor'], 404);
        }

        $service->delete();

        // Remove from vendor services array
        $vendor = Vendor::find($vendorId);
        $services = $vendor->services ?? [];
        $services = array_filter($services, fn($id) => $id !== $data['serviceId']);
        $vendor->services = array_values($services);
        $vendor->save();

        return response()->json(['message' => 'Service deleted successfully'], 200);
    }
}
