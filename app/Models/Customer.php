<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function devices()
    {
        return $this->hasMany('App\Models\Device','customer_id');
    }
}
