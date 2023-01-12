<?php

namespace Modules\Cinema\Entities;

use App\Models\Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CinemaLocation extends Base
{
    use HasFactory;

    protected $fillable = [
        'address',
        'latitude',
        'longitude',
    ];

    protected static function newFactory()
    {
        return \Modules\Cinema\Database\factories\CinemaLocationFactory::new();
    }

    public function cinema() {
        return $this->belongsTo(Cinema::class, 'owner_id', 'id');
    }
}
