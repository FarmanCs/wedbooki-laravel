<?php

namespace App\Src\Services\Vendor;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class S3Service
{
    //Upload a file to S3 and return its URL

    public function uploadFile(UploadedFile $file): string
    {
        // Generate a unique filename
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        $path = 'uploads/' . date('Y/m/d');

        try {
            // Upload file to S3 disk
            Storage::disk('s3')->putFileAs($path, $file, $filename, 'public');

            // Return the full URL
            return Storage::disk('s3')->url($path . '/' . $filename);
        } catch (\Exception $e) {
            Log::error('S3 Upload Error: ' . $e->getMessage());
            throw new \RuntimeException('Failed to upload file to S3.');
        }
    }

    // Delete a single file from S3 using its URL

    public function deleteByUrl(string $url): bool
    {
        try {
            // Parse path from URL
            $parsedUrl = parse_url($url, PHP_URL_PATH);
            $path = ltrim($parsedUrl ?? '', '/');

            // Remove bucket name prefix if present
            $bucket = config('filesystems.disks.s3.bucket');
            $path = str_replace($bucket . '/', '', $path);

            if (Storage::disk('s3')->exists($path)) {
                return Storage::disk('s3')->delete($path);
            }

            return false;
        } catch (\Exception $e) {
            Log::error('S3 Delete Error: ' . $e->getMessage());
            return false;
        }
    }

    // Delete multiple files from S3 using an array of URLs

    public function deleteMultiple(array $urls): int
    {
        $deletedCount = 0;

        foreach ($urls as $url) {
            if ($this->deleteByUrl($url)) {
                $deletedCount++;
            }
        }

        return $deletedCount;
    }
}
