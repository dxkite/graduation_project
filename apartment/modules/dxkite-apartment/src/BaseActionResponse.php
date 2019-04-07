<?php
namespace dxkite\apartment\response;

use suda\framework\Request;
use dxkite\apartment\controller\UserController;
use dxkite\apartment\controller\ApartmentController;

class BaseActionResponse extends UserSignResponse
{
    public function onUserVisit(Request $request)
    {
        $ctr=new UserController;
        $apt =new ApartmentController;
        if ($apt->isClose()) {
            return $this->onClose();
        } else {
            if ($ctr->isBinded($this->visitor->getId())) {
                return $this->onBinded();
            } else {
                return $this->onNotBind();
            }
        }
    }

    public function onNotBind()
    {
        $this->goRoute('bind');
    }

    public function onClose()
    {
        $this->goRoute('index');
    }
    
    public function onBinded()
    {
        $this->goRoute('home');
    }
}
