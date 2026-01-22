<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class ImageUpload
{

    /**
     * Upload file to given path with provided filename
     *
     * @param UploadedFile $file
     * @param string $folderPath   ex: verify-documents/
     * @param string $fileName     ex: aadhar_1700000000.jpg
     * @param string $disk         default: public
     *
     * @return string              Stored file path
     */
    public static function upload(UploadedFile $file, string $folderPath, string $fileName, string $disk = 'public'): ?string
    {
        try {
            // Ensure folder path has no trailing slash
            $folderPath = trim($folderPath, '/');

            // Store file
            $file->storeAs($folderPath, $fileName, $disk);

            // Return path to store in DB
            return $folderPath . '/' . $fileName;
        } catch (\Exception $e) {
            Log::error('File Upload Error: ' . $e->getMessage());
            return null; // Or throw an exception if you want
        }
    }

    /**
     * Delete file from storage
     *
     * @param string $filePath
     * @param string $disk
     * @return bool
     */
    public static function delete(string $filePath, string $disk = 'public'): bool
    {
        try {
            if ($filePath && Storage::disk($disk)->exists($filePath)) {
                return Storage::disk($disk)->delete($filePath);
            }

            return false;
        } catch (\Exception $e) {
            Log::error('File Delete Error: ' . $e->getMessage());
            return false;
        }
    }
}
