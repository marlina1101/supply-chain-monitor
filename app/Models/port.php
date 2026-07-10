<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Port extends Model
{
    protected $fillable = [
        'name',
        'country',
        'region',
        'latitude',
        'longitude',
        'volume',
        'status',
        'source',
        'external_ref',
        'synced_at',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
        'volume'    => 'float',
        'synced_at' => 'datetime',
    ];

    protected $appends = ['lat', 'lon'];

    public function getLatAttribute()
    {
        return $this->latitude;
    }

    public function getLonAttribute()
    {
        return $this->longitude;
    }
}