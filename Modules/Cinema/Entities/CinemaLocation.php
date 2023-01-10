<?php

namespace Modules\Cinema\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CinemaLocation extends Model
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
        return $this->belongsTo(Cinema::class);
    }
}
