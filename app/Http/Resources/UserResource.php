<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Provider\WorkingDaysResource;
use App\Http\Resources\Api\Settings\CityResource;

class UserResource extends JsonResource {

    private $token = '';
    public function setToken($value) {
        $this->token = $value;
        return $this;
    }

    public function toArray($request) {
        return [
            'name'   => $this->name,
            'email'  => $this->email,
            'token'  => $this->token,
        ];
    }
}
