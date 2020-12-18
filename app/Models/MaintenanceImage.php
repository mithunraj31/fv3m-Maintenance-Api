<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'maintenance_id'
    ];
    protected $appends = ['full_url'];

    public function getFullUrlAttribute() {
        return config('aws.s3.base_url').$this->url;
    }
    public function maintenance()
    {
        return $this->belongsTo('App\Models\Maintenance','maintenance_id');
    }
}
