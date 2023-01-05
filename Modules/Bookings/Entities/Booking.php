<?php

namespace Modules\Bookings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Movies\Entities\MovieSlot as MovieSlot;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_slot_id',
        'user_id'
    ];

    public function movieSlot() {
        return $this->hasOne(MovieSlot::class,'id','movie_slot_id');
    }

    public function user() {
        return $this->hasOne('App\Models\User','id','user_id');
    }

    protected static function newFactory()
    {
        return \Modules\Bookings\Database\factories\BookingFactory::new();
    }
}
