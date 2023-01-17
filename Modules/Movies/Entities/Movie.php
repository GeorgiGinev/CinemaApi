<?php

namespace Modules\Movies\Entities;

use App\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Cinema\Entities\Cinema;

class Movie extends Base
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'description',
        'release_date',
        'image',
        'deleted_at',
        'owner_id'
    ];

    public function slots() {
        return $this->hasMany(MovieSlot::class);
    }

    public function getCinema($slot) {
        $foundSlot = MovieSlot::where('id', $slot->id)->with('cinema')->first();
        return $foundSlot;
    }

    protected static function newFactory()
    {
        return \Modules\Movies\Database\factories\MovieFactory::new();
    }
}
