<?php

namespace App\Src\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    /**
     * Upload a file to local storage and return its URL
     *
     * @param UploadedFile $file
     * @param string $folder
     * @return string
     */
    public function upload(UploadedFile $file, string $folder = 'profile_images'): string
    {
        // Store the file in storage/app/public/profile_images
        $path = $file->store($folder, 'public');

        // Return full URL for public access
        return asset('storage/' . $path);
    }

    /**
     * Upload a file to S3 and return the public URL
     *
     * @param UploadedFile $file
     * @param string $folder
     * @return string
     */
    public function uploadToS3(UploadedFile $file, string $folder = 'profile_images'): string
    {
        // Store file on S3
        $path = Storage::disk('s3')->put($folder, $file);

        // Return public URL
        return Storage::disk('s3')->url($path);
    }
}
