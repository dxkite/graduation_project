<?php
namespace support\setting\provider;

use support\setting\Visitor;
use support\setting\UserSession;
use support\setting\exception\UserException;
use support\setting\controller\UserController;
use support\setting\controller\VisitorController;

class VisitorProvider extends UserSessionAwareProvider
{

    /**
     * 获取用户
     *
     * @param \support\setting\UserSession $session
     * @return Visitor
     */
    public function getVisitor(UserSession $session):Visitor
    {
        $visitor = new Visitor($session->getUserId());
        $ctr = new VisitorController;
        $visitor->setPermission($ctr->loadPermission($session->getUserId()));
        $uCtr = new UserController;
        $data = $uCtr->getInfoById($session->getUserId());
        $visitor->setAttributes($data?$data->toArray():[]);
        return $visitor;
    }
}
