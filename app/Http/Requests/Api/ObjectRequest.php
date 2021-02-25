<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Api\SanitizesRequest;
use App\Http\Requests\Api\ApiRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
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
        if(!json_decode($this->getContent())){
            $response = new Response([
                'http_code' => 400,
                'message' => 'Invalid Json Body Content in Request'
            ], 400);
            throw new HttpResponseException($response);
        }

        $inputKey = array_key_first($this->input()) ?? 'key';
        $rules = [
            $inputKey =>  'required'
        ];
        if(!array_key_first($this->input())){
            $rules['key'] = 'required';
        }
        $inputValidator = Validator::make(
            $this->input(),
            $rules
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