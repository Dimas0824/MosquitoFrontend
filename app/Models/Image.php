<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'device_id',
        'device_code',
        'image_type',
        'image_path',
        'width',
        'height',
        'checksum',
        'captured_at',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
        'uploaded_at' => 'datetime',
        'width' => 'integer',
        'height' => 'integer',
    ];

    const UPDATED_AT = null;

    /**
     * Get the inference result for this image
     */
    public function inferenceResult()
    {
        return $this->hasOne(InferenceResult::class, 'image_id');
    }

    /**
     * Get the device that owns this image
     */
    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }
}
