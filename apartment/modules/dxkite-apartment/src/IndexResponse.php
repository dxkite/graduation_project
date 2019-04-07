<?php
namespace dxkite\apartment\response;

use suda\framework\Request;
use dxkite\apartment\response\UserResponse;

class IndexResponse extends UserResponse
{
    public function onGuestVisit(Request $request)
    {
        $uri = $this->getUrl('dxkite/open-client@open-method:user', ['_method' => 'signin','redirect_uri' => $request->getUrl()]);
        $url = $this->application->getUribase($this->request). $uri;
        $view = $this->view('index');
        $view->set('title', '涉外学院宿舍选择系统');
        $view->set('signin', $url);
        $view->set('guest', true);
        return $view;
    }

    public function onUserVisit(Request $request)
    {
        $view = $this->view('index');
        $view->set('user', $this->visitor->getAttributes());
        return $view;
    }
}
