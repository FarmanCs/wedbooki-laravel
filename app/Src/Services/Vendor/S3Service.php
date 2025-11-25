<?php

namespace App\Src\Services\Vendor;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class S3Service
{
    /**
     * Upload file to S3
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string URL of uploaded file
     */
    public function uploadFile($file): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        $path = 'uploads/' . date('Y/m/d') . '/' . $filename;

        Storage::disk('s3')->put($path, file_get_contents($file), 'public');

        return Storage::disk('s3')->url($path);
    }

    /**
     * Delete file from S3 by URL
     *
     * @param string $url
     * @return bool
     */
    public function deleteByUrl(string $url): bool
    {
        try {
            // Extract path from URL
            $parsedUrl = parse_url($url);
            $path = ltrim($parsedUrl['path'] ?? '', '/');

            // Remove bucket name from path if present
            $bucketName = config('filesystems.disks.s3.bucket');
            $path = str_replace($bucketName . '/', '', $path);

            if (Storage::disk('s3')->exists($path)) {
                return Storage::disk('s3')->delete($path);
            }

            return false;
        } catch (\Exception $e) {
            \Log::error('S3 Delete Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete multiple files from S3
     *
     * @param array $urls
     * @return int Number of files deleted
     */
    public function deleteMultiple(array $urls): int
    {
        $deleted = 0;
        foreach ($urls as $url) {
            if ($this->deleteByUrl($url)) {
                $deleted++;
            }
        }
        return $deleted;
    }
}
