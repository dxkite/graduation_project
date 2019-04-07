<?php
namespace dxkite\apartment\response;

use suda\framework\Request;
use dxkite\apartment\response\UserResponse;

abstract class UserSignResponse extends UserResponse
{
    public function onGuestVisit(Request $request)
    {
        $uri = $this->getUrl('dxkite/open-client@open-method:user', ['_method' => 'signin','redirect_uri' => $request->getUrl()]);
        $url = $this->application->getUribase($this->request). $uri;
        $this->response->redirect($url);
    }
    
    abstract public function onUserVisit(Request $request);
}
