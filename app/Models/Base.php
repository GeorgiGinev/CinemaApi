<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    use HasFactory;

    /**
     * Turn this item object into a generic array
     */
    public function transform($relationships = null) {
        $transform = (object) [
            'id' => $this->id.'',
            'attributes' => (object) [],
            'relationships' => (object) []
        ];

        $attibutes = [];
        foreach ($this->fillable as $key) {
            $attibutes[$key] = $this[$key];
        }

        $transform->attributes = (object) $attibutes;

        if($relationships){
            $rel = [];

            for ($i = 0; $i < count($relationships); $i++) {
                $rel[$relationships[$i]] = $this[$relationships[$i]]->transform();
            }

            $transform->relationships = (object) $rel;
        }

        return $transform;
    }
}
