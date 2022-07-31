<?php

namespace App\Traits;

use App\Http\Resources\UserResource;

trait ResponseTrait {

    /**
     * keys : success, fail, needActive, waitingApprove, unauthenticated, blocked, exception
     */
    //todo: user builder design pattern
    public function response($key, $msg, $data = [], $anotherKey = [], $page = false) {

        $allResponse['key'] = (string) $key;
        $allResponse['msg'] = (string) $msg;

        # additional data
        if (!empty($anotherKey)) {
            foreach ($anotherKey as $otherkey => $value) {
                $allResponse[$otherkey] = $value;
            }
        }

        # res data
        if ( (in_array($key, ['success', 'needActive', 'exception']))){

            $allResponse['data'] = $data;
        }

        return response()->json($allResponse);
    }

    public function unauthenticatedReturn(){

        return $this->response('unauthenticated','unauthenticated');
    }

    public function unauthorizedReturn($otherData){

        return $this->response('unauthorized', 'not_authorized', [], $otherData);
    }

    public function failMsg($msg) {
        return $this->response('fail', $msg);
    }

    public function successMsg($msg = 'done'){

        return $this->response('success', $msg);
    }

    public function successData($data) {
        return $this->response('success', trans('apis.success'), $data);
    }

    public function successOtherData(array $dataArr) {
        return $this->response('success', trans('apis.success'), [], $dataArr);
    }


}

