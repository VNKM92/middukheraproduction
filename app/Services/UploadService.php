<?php

namespace App\Services;

use App\Models\MediaFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadService
{
    /**
     * Store an uploaded file and register it in the Media Library.
     *
     * @param UploadedFile $file
     * @param string $folder Subfolder under uploads/ (e.g. 'packages', 'blogs', 'gallery', 'pages', 'settings', 'media')
     * @return array Returns ['path' => ..., 'url' => ..., 'media' => MediaFile]
     */
    public static function upload(UploadedFile $file, string $folder = 'media'): array
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $safeName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $extension;
        
        $path = $file->storeAs("uploads/{$folder}", $safeName, 'public');
        $url = asset('storage/' . $path);
        $size = $file->getSize() ?: 0;
        $mimeType = $file->getMimeType() ?: 'application/octet-stream';

        $mediaFile = MediaFile::create([
            'filename' => $safeName,
            'original_name' => $originalName,
            'path' => $path,
            'url' => $url,
            'mime_type' => $mimeType,
            'size' => $size,
            'folder' => $folder,
        ]);

        return [
            'path' => $path,
            'url' => $url,
            'media' => $mediaFile,
        ];
    }

    /**
     * Delete an uploaded file from disk and database
     */
    public static function delete(MediaFile $mediaFile): bool
    {
        if (Storage::disk('public')->exists($mediaFile->path)) {
            Storage::disk('public')->delete($mediaFile->path);
        }
        return (bool)$mediaFile->delete();
    }
}
