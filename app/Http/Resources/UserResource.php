<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
