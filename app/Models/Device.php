<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
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
        'customer_id',
        'status_id',
        'serial_number',
        'regist_date',
        'mutated_date',
        'os',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer','customer_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Status','status_id');
    }

    public function maintenances()
    {
        return $this->hasMany('App\Models\Maintenance','device_id');
    }

    public function images()
    {
        return $this->hasMany('App\Models\DeviceImage','device_id');
    }
}
