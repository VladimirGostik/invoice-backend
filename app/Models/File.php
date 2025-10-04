<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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

}
