<?php


namespace dxkite\openuser\response\reset;


use dxkite\openuser\response\UserResponse;
use suda\framework\Request;

abstract class Response  extends UserResponse
{

    public function onGuestVisit(Request $request)
    {
        return $this->onVisit($request);
    }

    public function onUserVisit(Request $request)
    {
        return $this->onVisit($request);
    }

    abstract function onVisit(Request $request);
}