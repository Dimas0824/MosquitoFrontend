<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InferenceResult extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'image_id',
        'device_id',
        'device_code',
        'inference_at',
        'raw_prediction',
        'total_objects',
        'total_jentik',
        'total_non_jentik',
        'avg_confidence',
        'parsing_version',
        'status',
        'error_message',
    ];

    protected $casts = [
        'inference_at' => 'datetime',
        'raw_prediction' => 'array',
        'total_objects' => 'integer',
        'total_jentik' => 'integer',
        'total_non_jentik' => 'integer',
        'avg_confidence' => 'float',
    ];

    const UPDATED_AT = null;

    /**
     * Get the image that owns this inference result
     */
    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    /**
     * Get the device that owns this inference result
     */
    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }
}
