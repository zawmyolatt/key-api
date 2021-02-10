<?php

namespace App\Http\Requests\Api\ValidationErrorSerializer;

use Illuminate\Validation\Validator;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Serializer\DataArraySerializer;

class ValidationErrorSerializer extends DataArraySerializer
{
    public function serialize(Validator $validator)
    {
        return $validator->errors();
    }
}