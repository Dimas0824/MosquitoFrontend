<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceAuth extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'device_auth';

    public $timestamps = false;

    protected $fillable = [
        'device_id',
        'device_code',
        'password_hash',
    ];
}
