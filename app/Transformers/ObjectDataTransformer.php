<?php

namespace App\Transformers;

use App\Models\ObjectData;
use League\Fractal\TransformerAbstract;

class ObjectDataTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];

    /**
     * Transform ObjectData model
     * @param  ObjectData   $objectData
     * @return array
     */
    public function transform(ObjectData $objectData)
    {
        return [
            'id' => $objectData->id,
            'key' => $objectData->key,
            'value' => $objectData->value,
            'created_at' => (string) $objectData->created_at
        ];
    }

}