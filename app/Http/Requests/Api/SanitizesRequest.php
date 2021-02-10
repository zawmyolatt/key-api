<?php

namespace App\Http\Requests\Api;

use Illuminate\Http\Request;

trait SanitizesRequest
{
    public function sanitizeRequest(Request &$request, array $fields)
    {
        $input = $request->all();
        $search = config("request.sanitize");

        foreach ($fields as $field) {
            if (!empty($search[$field])) {
                $input[$field] = str_replace($search[$field], '', $request->$field);
            }
        }

        $request->replace($input);
    }
}