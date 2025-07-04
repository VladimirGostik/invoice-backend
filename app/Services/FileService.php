<?php

namespace App\Services;

use App\Models\File;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    public function upload(UploadedFile $file, Model $fileable, string $collection, string $disk = 'public'): File
    {
        return $this->storeFile($fileable,
            $collection,
            $disk,
            [
                'extension' => $file->getClientOriginalExtension(),
                'contents' => $file->getContent(),
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(), // in bytes
            ]);
    }

    /**
     * @throws Exception
     */
    public function uploadBase64(string $base64Image, Model $fileable, string $collection, string $disk = 'public'): File
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64Image)) {
            throw new \Exception('Invalid image type');
        }

        $base64 = substr($base64Image, strpos($base64Image, ',') + 1);
        $extension = explode('/', explode(':', substr($base64Image, 0, strpos($base64Image, ';')))[1])[1];
        $contents = base64_decode($base64);
        $size = strlen($contents); // in bytes

        return $this->storeFile($fileable,
            $collection,
            $disk,
            [
                'extension' => $extension,
                'contents' => $contents,
                'size' => $size,
            ]);
    }

    public function uploadPdf(string $pdf, Model $fileable, string $collection, string $disk = 'public'): string
    {
        $file = $this->storeFile($fileable,
            $collection,
            $disk,
            [
                'extension' => 'pdf',
                'contents' => $pdf,
                'size' => strlen($pdf),
            ]);

        return $this->getPath($file);
    }

    public function getUrl(File $file): string
    {
        return Storage::disk($file->disk)->url($file->path);
    }

    public function getPath(File $file): string
    {
        return Storage::disk($file->disk)->path($file->path);
    }

    public function delete(File $file): bool
    {
        return Storage::disk($file->disk)->delete($file->path);
    }

    /**
     * @param array{
     *     extension: string,
     *     contents: string,
     *     original_filename?: string,
     *     mime_type?: string,
     *     size?: int
     * } $data
     */
    private function storeFile(Model $fileable, string $collection, string $disk, array $data): File
    {
        $imageName = Str::uuid() . '-' . time() . '.' . $data['extension'];
        Storage::disk($disk)->put($collection . '/' . $imageName, $data['contents']);
        $path = $collection . '/' . $imageName;

        return File::create([
            'fileable_id' => $fileable->id,
            'fileable_type' => get_class($fileable),
            'path' => $path,
            'filename' => $imageName,
            'original_filename' => $data['original_filename'] ?? null,
            'mime_type' => $data['mime_type'] ?? null,
            'size' => $data['size'] ?? null,
            'disk' => $disk,
            'collection' => $collection,
        ]);
    }

    /**
     * Use for controller uploads with handling
     */
    public function handleUpload(
        Request $request,
        Model $fileable,
        string $collection,
        string $fileName,
        string $modelRelationship,
        string $disk = 'public',
    ): void
    {
        if (!method_exists($fileable, $modelRelationship) && !property_exists($fileable, $modelRelationship)) {
            throw new \InvalidArgumentException("Relationship '$modelRelationship' does not exist on " . get_class($fileable));
        }

        // Delete the file if a removal request is present
        if ($request->boolean("{$fileName}_remove")) {
            $this->deleteFileIfExists($fileable, $modelRelationship);
        }

        // If it has file, perform upload, and also delete file if exists
        if ($request->hasFile("$fileName")) {
            $this->deleteFileIfExists($fileable, $modelRelationship);
            $this->upload($request->file("$fileName"), $fileable, $collection, $disk);
        }
    }

     private function deleteFileIfExists(Model $model, string $modelRelationship): void
    {
        if (!method_exists($model, $modelRelationship) && !property_exists($model, $modelRelationship)) {
            throw new \InvalidArgumentException("Relationship '$modelRelationship' does not exist on " . get_class($model));
        }

        $related = $model->$modelRelationship;

        if ($related instanceof File) {
            $this->delete($related);
            $related->delete();
            return;
        }

        if (is_iterable($related)) {
            foreach ($related as $file) {
                if ($file instanceof File) {
                    $this->delete($file);
                    $file->delete();
                }
            }
        }
    }

    public function handleDelete(Model $model, string $relationship = 'files'): void
    {
        if (! method_exists($model, $relationship) && ! property_exists($model, $relationship)) {
            throw new \InvalidArgumentException("Relationship '$relationship' must be defined on " . get_class($model));
        }

        $related = $model->$relationship;

        // 1) single File
        // If the relationship is a single File instance, delete it
        if ($related instanceof File) {
            $this->delete($related);
            $related->delete();
            return;
        }

        // 2) multiple Files
        // If related is not iterable, we assume it's a single File instance
        if (is_iterable($related)) {
            foreach ($related as $file) {
                if ($file instanceof File) {
                    $this->delete($file);
                    $file->delete();
                }
            }
        }
    }


    public function handleUploadMultiple(
        Request $request,
        Model $model,
        string $collection,
        string $fileKey = 'files',
        string $modelRelationship = 'files',
        string $disk = 'public'
    ): void {
        if ($request->has('files_for_delete')) {
            $files = $model->files()->whereIn('id', $request->files_for_delete)->get();
            foreach ($files as $file) {
                $file->delete();
                $this->delete($file);
            }
        }
        if ($request->hasFile($fileKey)) {
            $this->deleteFileIfExists($model, $modelRelationship);

            foreach ($request->file($fileKey) as $file) {
                $this->upload(
                    $file,
                    $model,
                    $collection,
                    $disk
                );
            }
        }
    }

    /**
     * Use for controller uploads with handling
     */
    public function uploadWithoutReplacement(
        Request $request,
        Model $fileable,
        string $collection,
        string $fileName,
        string $modelRelationship,
        string $disk = 'public',
    ): void
    {
        if (!method_exists($fileable, $modelRelationship) && !property_exists($fileable, $modelRelationship)) {
            throw new \InvalidArgumentException("Relationship '$modelRelationship' does not exist on " . get_class($fileable));
        }
        $this->upload($request->file("$fileName"), $fileable, $collection, $disk);
    }
}
