<?php

namespace App\Http\Requests\API;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ResponseTrait;

class LoginRequest extends FormRequest {

    use ResponseTrait;

    public function rules() {
        return [
            'email'     => 'required|email|exists:users,email|max:50',
            'password'  => 'required|min:6|max:100',
        ];
    }

    protected function failedValidation(Validator $validator){

        throw new HttpResponseException($this->response('fail', $validator->errors()->first()));
    }

}
