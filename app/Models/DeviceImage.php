<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'device_id'
    ];

    protected $appends = ['full_url'];

    public function device()
    {
        return $this->belongsTo('App\Models\Device','device_id');
    }
    public function getFullUrlAttribute() {
        return config('aws.s3.base_url').$this->url;
    }
}
