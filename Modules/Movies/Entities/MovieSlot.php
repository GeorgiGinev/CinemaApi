<?php

namespace Modules\Movies\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MovieSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id',
        'date'
    ];

    public function movie() {
        return $this->hasOne(Movie::class,'id','movie_id');
    }

    protected static function newFactory()
    {
        return \Modules\Movies\Database\factories\MovieSlotFactory::new();
    }
}
