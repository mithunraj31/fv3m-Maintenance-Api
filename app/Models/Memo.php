<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Memo extends Model
{
    use HasFactory,SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'maintenance_id',
        'description',
        'lat',
        'lng'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function maintenance()
    {
        return $this->belongsTo('App\Models\Maintenance','maintenance_id');
    }

    public function images()
    {
        return $this->hasMany('App\Models\MemoImage','memo_id');
    }
}
