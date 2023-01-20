<?php

namespace Modules\Bookings\Entities;

use App\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Cinema\Entities\Cinema;
use Modules\Movies\Entities\MovieSlot as MovieSlot;

class Booking extends Base
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'movie_slot_id',
        'cinema_id',
        'user_id',
        'places',
        'deleted_at'
    ];

    protected $casts = [
        'places' => 'json'
    ];

    public function movieSlot() {
        return $this->hasOne(MovieSlot::class,'id','movie_slot_id');
    }

    public function cinema() {
        return $this->belongsTo(Cinema::class);
    }

    public function user() {
        return $this->hasOne('App\Models\User','id','user_id');
    }

    protected static function newFactory()
    {
        return \Modules\Bookings\Database\factories\BookingFactory::new();
    }
}
