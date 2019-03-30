<?php
namespace support\setting\response\user;

use suda\framework\Request;
use support\setting\response\Response;

class SigninResponse extends Response
{
    public function onGuestVisit(Request $request)
    {
        return $this->view('signin')->set('title', '用户登陆');
    }
    
    public function onAccessVisit(Request $request)
    {
        $this->goRoute('support/setting:index');
    }
}
