<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maintenance extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'lat',
        'lng',
        'device_id',
    ];


    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function device()
    {
        return $this->belongsTo('App\Models\Device','device_id');
    }

    public function memos()
    {
        return $this->hasMany('App\Models\Memo','maintenance_id');
    }

    public function images()
    {
        return $this->hasMany('App\Models\MaintenanceImage','maintenance_id');
    }
}
