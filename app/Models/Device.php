<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

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
        'status_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Customer','customer_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Status','status_id');
    }

    public function maintenances()
    {
        return $this->belongsTo('App\Maintenance','device_id');
    }
}
