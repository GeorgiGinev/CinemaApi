<?php

namespace Modules\Movies\Entities;

use App\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Base
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'description',
        'release_date',
        'image',
        'deleted_at'
    ];

    protected static function newFactory()
    {
        return \Modules\Movies\Database\factories\MovieFactory::new();
    }
}
