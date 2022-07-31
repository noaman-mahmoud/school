<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\Api\UserResource;
use App\Traits\ResponseTrait;
use App\Models\User;
use Auth;
use Hash;

class AuthController extends Controller {

    use ResponseTrait;

    public function login(LoginRequest $request) {

        $user = User::where('email', $request['email'])->first();

        if (!Hash::check($request->password, $user->password)) {
            return $this->failMsg('auth failed');
        }

        return $this->response('success', __('apis.signed'), ['user' => $user->login()]);
    }

}

