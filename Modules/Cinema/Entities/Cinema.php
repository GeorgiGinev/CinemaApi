<?php

namespace Modules\Cinema\Entities;

use App\Models\Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cinema extends Base
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'images',
        'logo',
        'capacity',
        'deleted_at'
    ];

    public function cinemaLocation() {
        $location = $this->hasOne(CinemaLocation::class, 'id', 'cinema_location_id');
        
        return $location;
    }

    public function owner() {
        return $this->hasOne('App\Models\User','id','owner_id');
    }

    protected static function newFactory()
    {
        return \Modules\Cinema\Database\factories\CinemaFactory::new();
    }
}
