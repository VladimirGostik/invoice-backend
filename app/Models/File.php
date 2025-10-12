<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $fillable = [
        'fileable_id',
        'fileable_type',
        'path',
        'filename',
        'original_filename',
        'mime_type',
        'size',
        'disk',
        'collection',
    ];

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getPath(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }
}
