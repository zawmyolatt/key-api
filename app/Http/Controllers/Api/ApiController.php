<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ValidationErrorSerializer\PlainArraySerializer;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Validator;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class ApiController extends Controller
{
    protected $fractal;
    protected $statusCode = 200;

    public function __construct(Manager $fractal)
    {
        $this->fractal = $fractal;
        $this->fractal->setSerializer(new PlainArraySerializer());
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    protected function respondWithItem($item, $transformer, $resourceKey = null, $includes = [], $statusCode = 200, $metadata = null)
    {
        return $this->respondWithArray(
            $this->transformItem($item, $transformer, $resourceKey, $includes, $metadata),
            [],
            $statusCode
        );
    }

    protected function respondWithArray(array $array, array $headers = [], $statusCode = 200)
    {
        $this->setStatusCode($statusCode);

        $response = Response::json($array, $this->statusCode, $headers);
        $response->header('Content-Type', 'application/json');

        return $response;
    }

    protected function respondWithError($httpCode = 400, $message = '', $errors = [])
    {
        $this->setStatusCode($httpCode);

        return $this->respondWithArray(
            [
                'http_code' => $this->statusCode,
                'message' => $message,
                'errors' => $errors,
            ],
            [],
            $this->statusCode
        );
    }

    protected function respondWithUnexpectedError($messageKey = 'unexpected_error')
    {
        $message = trans("errors.$messageKey");
        return $this->respondWithError(500, $message);
    }

    protected function respondWithFieldError($errorKey,$field, $httpCode = 400, $messageKey = 'bad_request', $translationParameters = []) 
    {
        if (is_array($errorKey)) {
            $errorMessages = [];
            foreach ($errorKey as $translationKey) {
                $errorMessages[] = trans("errors.$translationKey", $translationParameters);
            }
        } else {
            $errorMessages[] = trans("errors.$errorKey", $translationParameters);
        }

        $errors[$field] = $errorMessages;
        $message = trans("errors.$messageKey");

        return $this->respondWithError($httpCode, $message, $errors);
    }

    protected function respondWithValidationError(Validator $validator, $httpCode = 400, $messageKey = 'bad_request')
    {
        $message = trans("errors.$messageKey");
        return $this->respondWithError($httpCode, $message, $validator->errors());
    }

    protected function respondWithCollection($collection, $transformer, $includes = [], $metadata = null)
    {
        return $this->respondWithArray(
            $this->transformCollection($collection, $transformer, $includes, $metadata)
        );
    }

    protected function respondWithNoContent(array $headers = [], $statusCode = 204)
    {
        $this->setStatusCode($statusCode);

        $response = Response::json(null, $this->statusCode, $headers);
        $response->header('Content-Type', 'application/json');

        return $response;
    }

    protected function transformCollection($collection, $transformer, $includes = [], $metadata = null)
    {
        if (!empty($includes)) {
            $this->fractal->parseIncludes($includes);
        }

        $resource = new Collection($collection, $transformer);
        if (!empty($metadata)) {
            $resource->setMeta($metadata);
        }

        $dataFromResource = $this->fractal->createData($resource);

        return $dataFromResource->toArray();
    }

    protected function transformItem($item, $transformer, $resourceKey, $includes = [], $metadata = null)
    {
        if (!empty($includes)) {
            $this->fractal->parseIncludes($includes);
        }

        $resource = new Item($item, $transformer, $resourceKey);
        if (!empty($metadata)) {
            $resource->setMeta($metadata);
        }
        
        $dataFromResource = $this->fractal->createData($resource);

        return $dataFromResource->toArray();
    }
}