<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Api\SanitizesRequest;
use App\Http\Requests\Api\ApiRequest;
use Illuminate\Support\Facades\Validator;

class ObjectRequest extends ApiRequest
{
    use SanitizesRequest;

    public function getValidatorsForIndex()
    {
        $inputValidator = Validator::make(
            $this->input(),
            [
                'page' =>  'integer',
                'limit' =>  'integer|max:30'
            ]
        );

        return [
            ['validator' => $inputValidator, 'status_code' => 400, 'message_key' => 'bad_request']
        ];
    }

    public function getValidatorsForStore()
    {
        $inputKey = array_key_first($this->input()) ?? 'key';
        $inputValidator = Validator::make(
            $this->input(),
            [
                $inputKey =>  'required'
            ]
        );

        return [
            ['validator' => $inputValidator, 'status_code' => 400, 'message_key' => 'bad_request']
        ];
    }


    public function getValidatorsForShow()
    {
        $resourceValidator = Validator::make($this->route()->parameters(), [
            'key' => 'required|exists:objects,key',
        ]);

        $inputValidator = Validator::make(
            $this->input(),
            [
                'timestamp' =>  'integer'
            ]
        );

        return [
            ['validator' => $resourceValidator, 'status_code' => 404, 'message_key' => 'not_found'],
            ['validator' => $inputValidator, 'status_code' => 400, 'message_key' => 'bad_request']
        ];
    }
}