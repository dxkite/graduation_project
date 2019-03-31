<?php
namespace support\setting\response;

use suda\framework\Request;
use support\setting\response\Response;

abstract class SignedResponse extends Response
{
    public function onGuestVisit(Request $request)
    {
        $this->goRoute('support/setting:signin');
    }
    
    abstract public function onAccessVisit(Request $request);
}
