<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Api\ValidationErrorSerializer\ValidationErrorSerializer;
use App\Http\Requests\Api\ValidationErrorSerializer\PlainArraySerializer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\Validator;

class ApiRequest extends FormRequest
{
    protected $validationErrorSerializer;

    public function __construct()
    {
        parent::__construct();
        $this->validationErrorSerializer = new ValidationErrorSerializer();
    }

    /**
     * Validate the class instance.
     *
     * @return void
     */
    public function prepareForValidation()
    {
        $errorFormat = $this->get('errors_format', null);
        if($errorFormat == 'inline'){
            $this->validationErrorSerializer = new PlainArraySerializer();
        }

        if (!$this->passesAuthorization()) {
            $this->failedAuthorization();
        } else {
            // Get validators for the specific route action
            $routeActionMethod = ucfirst($this->getRouteActionMethod());
            $getValidatorMethodName = 'getValidatorsFor' . ($routeActionMethod ?? '');
            if (!method_exists($this, $getValidatorMethodName)) {
                throw new \Exception("Missing $getValidatorMethodName method");
            }

            $validators = call_user_func([$this, $getValidatorMethodName]);
            foreach ($validators as $validator) {
                if ($validator['validator']->fails()) {
                    $this->failedValidationWithStatusCode(
                        $validator['validator'],
                        $validator['status_code'],
                        $validator['message_key'] ?? '',
                        $this->validationErrorSerializer
                    );
                }
            }
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }

    public function validationData()
    {
        return $this->route()->parameters() + $this->input();
    }

    protected function failedValidationWithStatusCode(Validator $validator, $statusCode, $messageKey, $validationErrorSerializer)
    {
        $response = new Response([
            'http_code' => $statusCode,
            'message' => !empty($messageKey) ? trans("errors.$messageKey") : null,
            'errors' => $validationErrorSerializer->serialize($validator),
        ], $statusCode);

        throw new HttpResponseException($response);
    }

    protected function getRouteActionMethod()
    {
        $routeActionNameArray = explode('@', $this->route()->getActionName());
        return count($routeActionNameArray) > 1 ? $routeActionNameArray[1] : null;
    }
}
