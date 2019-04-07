<?php
namespace dxkite\apartment\response;

use suda\framework\Request;
use support\setting\VerifyImage;
use dxkite\apartment\response\UserResponse;

class VerifyResponse extends UserResponse
{
    public function onGuestVisit(Request $request)
    {
        $this->generateImage();
    }
    
    public function onUserVisit(Request $request)
    {
        $this->generateImage();
    }

    public function generateImage()
    {
        $verify = new VerifyImage($this->context, 'apartment');
        \ob_start();
        $verify->display();
        $content = \ob_get_clean();
        $response = $this->context->getResponse();
        $response->setType('jpeg');
        $response->send($content);
    }
}
