<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DeviceControl extends Model
{
    use HasUuids;

    protected $table = 'device_controls';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'device_id',
        'device_code',
        'control_command',
        'status',
        'message',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
