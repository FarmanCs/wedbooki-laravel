<?php

namespace App\Src\Services\Vendor;

use App\Models\Vendor\Business;
use Illuminate\Http\JsonResponse;

class VendorMediaService
{
    protected $s3Service;

    public function __construct(S3Service $s3Service)
    {
        $this->s3Service = $s3Service;
    }

    public function updateVendorPortfolioImages($businessId, array $files): JsonResponse
    {
        $business = Business::find($businessId);

        if (!$business) {
            return response()->json(['message' => 'Business not found'], 404);
        }

        $portfolioImages = $files['portfolio_images'] ?? [];

        if (count($portfolioImages) > 20) {
            return response()->json([
                'message' => 'Portfolio images must be less than 20.'
            ], 400);
        }

        // Upload new images
        $newPortfolioImageUrls = [];
        foreach ($portfolioImages as $image) {
            $newPortfolioImageUrls[] = $this->s3Service->uploadFile($image);
        }

        // Append to existing images
        $existingImages = $business->portfolio_images ?? [];
        $business->portfolio_images = array_unique(array_merge($existingImages, $newPortfolioImageUrls));
        $business->save();

        return response()->json([
            'message' => 'Images updated successfully',
            'portfolio_images' => $newPortfolioImageUrls
        ], 200);
    }

    public function deleteVendorPortfolioImage($businessId, array $data): JsonResponse
    {
        if (!isset($data['imageUrl'])) {
            return response()->json(['message' => 'Image URL is required.'], 400);
        }

        $business = Business::find($businessId);

        if (!$business) {
            return response()->json(['message' => 'Vendor not found.'], 404);
        }

        $currentImages = $business->portfolio_images ?? [];

        if (!in_array($data['imageUrl'], $currentImages)) {
            return response()->json(['message' => 'Image not found in vendor portfolio.'], 404);
        }

        // Delete from S3
        $this->s3Service->deleteByUrl($data['imageUrl']);

        // Remove from array
        $business->portfolio_images = array_values(array_filter($currentImages, function($img) use ($data) {
            return $img !== $data['imageUrl'];
        }));
        $business->save();

        return response()->json([
            'message' => 'Image deleted successfully.',
            'portfolio_images' => $business->portfolio_images
        ], 200);
    }

    public function updateVendorVideos($businessId, array $files): JsonResponse
    {
        $business = Business::find($businessId);

        if (!$business) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        $videoFiles = $files['videos'] ?? [];

        if (count($videoFiles) < 1) {
            return response()->json(['message' => 'Videos not selected'], 400);
        }

        // Upload videos
        $newVideoUrls = [];
        foreach ($videoFiles as $video) {
            $newVideoUrls[] = $this->s3Service->uploadFile($video);
        }

        // Append to existing videos
        $existingVideos = $business->videos ?? [];
        $business->videos = array_unique(array_merge($existingVideos, $newVideoUrls));
        $business->save();

        return response()->json([
            'message' => 'Videos updated successfully',
            'videos' => $newVideoUrls
        ], 200);
    }

    public function deleteVendorVideo($businessId, array $data): JsonResponse
    {
        if (!isset($data['videoUrl'])) {
            return response()->json(['message' => 'Video URL is required.'], 400);
        }

        $business = Business::find($businessId);

        if (!$business) {
            return response()->json(['message' => 'Vendor not found.'], 404);
        }

        $currentVideos = $business->videos ?? [];

        if (!in_array($data['videoUrl'], $currentVideos)) {
            return response()->json(['message' => 'Video not found in vendor profile.'], 404);
        }

        // Delete from S3
        $this->s3Service->deleteByUrl($data['videoUrl']);

        // Remove from array
        $business->videos = array_values(array_filter($currentVideos, function($v) use ($data) {
            return $v !== $data['videoUrl'];
        }));
        $business->save();

        return response()->json([
            'message' => 'Video deleted successfully.',
            'videos' => $business->videos
        ], 200);
    }
}
