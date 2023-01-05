<?php

namespace Modules\Movies\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'description',
        'release_date',
        'image'
    ];

    protected static function newFactory()
    {
        return \Modules\Movies\Database\factories\MovieFactory::new();
    }
}
