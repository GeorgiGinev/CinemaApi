<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TheSeer\Tokenizer\Exception;

class Base extends Model
{
    use HasFactory;

    /**
     * Turn this item object into a generic array
     */
    public function transform($relationships = null)
    {
        $transform = (object) [
            'id' => $this->id . '',
            'attributes' => (object) [],
            'relationships' => (object) []
        ];

        $attibutes = [];
        foreach ($this->fillable as $key) {
            $attibutes[$key] = $this[$key];
        }

        $transform->attributes = (object) $attibutes;

        if ($relationships) {
            $rel = [];

            for ($i = 0; $i < count($relationships); $i++) {
                if(is_object($this[$relationships[$i]])) {
                    $rel[$relationships[$i]] = $this[$relationships[$i]]->transform();
                } else {
                    count($this[$relationships[$i]]);

                    $rel[$relationships[$i]] = null;
                    $rel[$relationships[$i]]['data'] = [];
                    
                    foreach ($this[$relationships[$i]] as $resource) {
                        array_push($rel[$relationships[$i]]['data'], $resource->transform());
                    }
                }
            }

            $transform->relationships = (object) $rel;
        }

        return $transform;
    }

    // private function getRelationLevel($startObject, $relationship)
    // {
    //     $endIndex = strpos($relationship, '.') ? strpos($relationship, '.') : strlen($relationship);

    //     $relation = substr($relationship, 0, $endIndex);
    //     $nextRelation = substr($relationship, strlen($relation) + 1, strlen($relationship));

    //     $rel = [];
    //     try {
    //         count($startObject[$relation]);

    //         $rel[$relation] = null;
    //         $rel[$relation]['data'] = [];

    //         foreach ($startObject[$relation] as $resource) {
    //             $res = $this->getRelationLevel($resource, $nextRelation);

    //             if (!strpos($nextRelation, '.') && strlen($nextRelation) === 0) {
    //                 return array_push($rel[$relation]['data'], $resource);
    //             } else {
    //                 array_push($rel[$relation]['data'], $res);
    //             }
    //         }
    //     } catch (Exception $e) {

    //     }

    //     if (!strpos($nextRelation, '.') && strlen($nextRelation) === 0) {
    //         return $relation;
    //     }

    //     return $relation . $this->getRelationLevel($nextRelation);
    // }
}