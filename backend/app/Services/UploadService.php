<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    public function uploadFile(UploadedFile $file, string $directory = 'uploads'): string
    {
        return $file->store($directory, 'public');
    }

    public function uploadMultiple(array $files, string $directory = 'uploads'): array
    {
        $paths = [];

        foreach ($files as $file) {
            $paths[] = $file->store($directory, 'public');
        }

        return $paths;
    }

    public function deleteFile(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }
}
