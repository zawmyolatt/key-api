<?php

namespace App\Http\Requests\Api\ValidationErrorSerializer;

use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;

/**
 * Deserialize validation errors from array dot notation to associative array
 * Expected output:
 * ```
 * {
 *      "some_field": ["Some error message"]:
 *      "some_nested_field": [
 *          "yet_another_field": ["Yet another error message"]:
 *      ]
 * }
 * ```
 */
class PlainArraySerializer extends ValidationErrorSerializer
{
    public function serialize(Validator $validator)
    {
        $errors = [];
        foreach ($validator->errors()->messages() as $key => $error) {
            $this->setError($errors, $key, $error);
        }
        return $errors;
    }

    private function setError(&$errors, $field, $value, $separator = '.')
    {
        $keys = explode($separator, $field);
        foreach ($keys as $key) {
            if (is_string($errors)) {
                $errors = [$errors];
            }
            $errors = &$errors[$key];
        }

        $errors = $value;
    }
}