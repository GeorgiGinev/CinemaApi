<?php

namespace Modules\Movies\Entities;

use App\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Cinema\Entities\Cinema;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovieSlot extends Base
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'movie_id',
        'cinema_id',
        'price',
        'date'
    ];

    public function cinema() {
        return $this->hasOne(Cinema::class,'id','cinema_id');
    }

    public function movie() {
        return $this->belongsTo(Movie::class);
    }

    protected static function newFactory()
    {
        return \Modules\Movies\Database\factories\MovieSlotFactory::new();
    }
}
