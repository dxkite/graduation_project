<?php
namespace dxkite\apartment\response;

use suda\framework\Request;





class IndexResponse extends UserSignResponse
{
    
    public function onUserVisit(Request $request)
    {
        // $view = $this->view('home/index');
        // $view->set('title', '个人中心');
        // $view->set('user', $this->visitor->getAttributes());
        return $this->visitor->getAttributes();
    }
}
