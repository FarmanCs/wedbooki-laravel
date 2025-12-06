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
        return response()->json([
            'message' => 'Package created',
            'newPackage' => $package
        ], 200);
    }

    public function updatePackage($business_id, array $data): JsonResponse
    {
        // Validate package belongs to this business
        $package = Package::where('business_id', $business_id)
            ->where('id', $data['package_id'])
            ->first();

        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }

        // Remove package_id from update data
        unset($data['package_id']);

        //Discount Logic (Same as Node.js)
        if (isset($data['price'])) {

            if (!isset($data['discount'])) {
                // If only price updated → discount = price
                $data['discount'] = $data['price'];
                $data['discount_percentage'] = 0;
            } else {
                // price + discount provided → calculate %
                $data['discount_percentage'] =
                    (($data['price'] - $data['discount']) / $data['price']) * 100;
            }

        } elseif (isset($data['discount'])) {

            // Only discount updated → use DB price
            $price = $package->price;

            if ($price == 0) {
                return response()->json(['message' => 'Price cannot be zero'], 400);
            }

            $data['discount_percentage'] =
                (($price - $data['discount']) / $price) * 100;
        }

        // Update the package
        $package->update($data);

        return response()->json([
            'message'        => 'Package updated',
            'updatedPackage' => $package->fresh()
        ], 200);
    }

    public function deletePackage($businessId, $request): JsonResponse
    {
        $package = Package::where('id', $request['id'])->where('business_id', $businessId)->first();
        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }
        $package->delete();
        return response()->json(['message' => 'Package deleted'], 200);
    }

    public function getAllPackages($businessId): JsonResponse
    {
        $packages = Package::with('business')->where('business_id', $businessId)->get();

        if (!$packages || count($packages) === 0) {
            return response()->json(['message' => 'now package found for this business'], 404);
        }
        return response()->json([
            'status'=>'success',
            "message"=>'packages found successfully',
            'packages' => $packages], 200);
    }

    public function createService(array $data): JsonResponse
    {
        try {
            // Get authenticated vendor
            $vendor = auth()->user();

            if (!$vendor || $vendor->role !== 'vendor') {
                return response()->json([
                    'message' => 'Unauthorized or vendor not found.'
                ], 403);
            }

            // Create service
            $service = Service::create([
                'name'        => $data['name'],
                'description' => $data['description'],
                'price'       => $data['price'],
                'category'    => $data['category'],
                'vendor_id'   => $vendor->id,
            ]);

            // Attach service to vendor relationship (optional)
            $vendor->services()->save($service);

            return response()->json([
                'message' => 'Service created successfully',
                'service' => $service
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Please try again later.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function updateService($request): JsonResponse
    {
        try {
            // Get authenticated vendor
            $vendor = auth()->user();

            if (!$vendor || $vendor->role !== 'vendor') {
                return response()->json([
                    'message' => 'Unauthorized or vendor not found.'
                ], 403);
            }

            // Validate request
            $validated = $request->validate([
                'service_id'  => 'required|integer|exists:services,id',
                'title'       => 'required|string|max:255',
                'description' => 'nullable|string',
                'category'    => 'required|string|max:255',
                'price'       => 'required|numeric|min:0',
            ]);

            // Find service
            $service = Service::find($validated['service_id']);

            if (!$service) {
                return response()->json([
                    'message' => 'Service does not exist'
                ], 404);
            }

            // Check ownership
            if ($service->vendor_id !== $vendor->id) {
                return response()->json([
                    'message' => 'Not authorized to update this service'
                ], 401);
            }

            // Update service fields
            $service->update([
                'name'        => $validated['title'],
                'description' => $validated['description'] ?? $service->description,
                'category'    => $validated['category'],
                'price'       => $validated['price'],
            ]);

            return response()->json([
                'message' => 'Service updated successfully',
                'service' => $service->fresh()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Please try again later.',
                'error'   => $e->getMessage()
            ], 500);
        }
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
