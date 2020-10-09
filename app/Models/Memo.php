<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memo extends Model
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

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

    public function maintenance()
    {
        return $this->belongsTo('App\Maintenance','maintenance_id');
    }

    public function images()
    {
        return $this->hasMany('App\MemoImage','memo_id');
    }
}
