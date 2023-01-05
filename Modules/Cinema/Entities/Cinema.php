<?php

namespace Modules\Cinema\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User as User;

class Cinema extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'images',
        'logo',
        'capacity',
        'cinema_location_id',
        'owner_id',
    ];

    public function cinemaLocation() {
        return $this->hasOne(CinemaLocation::class,'id','cinema_location_id');
    }

    public function owner() {
        return $this->hasOne('App\Models\User','id','owner_id');
    }

    protected static function newFactory()
    {
        return \Modules\Cinema\Database\factories\CinemaFactory::new();
    }
}
