<?php


namespace dxkite\openclient\provider;


use dxkite\openclient\controller\UserController;
use dxkite\openuser\provider\VisitorAwareProvider;
use support\setting\Visitor;

class UserAwareProvider extends VisitorAwareProvider
{
    public function createVisitor(string $userId)
    {
        $user = new UserController;
        if (($data = $user->getById($userId)) !== null) {
            return new Visitor($userId, $data);
        } else {
            return new Visitor;
        }
    }
}