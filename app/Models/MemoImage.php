<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemoImage extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'memo_id'
    ];
    protected $appends = ['full_url'];

    public function getFullUrlAttribute() {
        return config('aws.s3.base_url').$this->url;
    }
    public function memo()
    {
        return $this->belongsTo('App\Models\Memo','memo_id');
    }
}
