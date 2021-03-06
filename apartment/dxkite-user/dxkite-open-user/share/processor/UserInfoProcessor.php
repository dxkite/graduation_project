<?php
namespace dxkite\openuser\processor;

use suda\framework\Request;
use suda\framework\Response;
use support\setting\Context;
use support\setting\Visitor;
use support\session\UserSession;
use suda\application\Application;
use dxkite\openuser\controller\UserController;
use suda\application\processor\RequestProcessor;

/**
 * 设置环境状态
 */
class UserInfoProcessor implements RequestProcessor
{
    /**
     * @param Application $application
     * @param Request $request
     * @param Response $response
     * @return mixed|Context
     * @throws \suda\database\exception\SQLException
     */
    public function onRequest(Application $application, Request $request, Response $response)
    {
        $context = new Context($application, $request, $response);
        $session = UserSession::createFromRequest($request, 'openuser', $application->conf("app.debug-key", ''));
        $user = new UserController;
        if (strlen($session->getUserId()) && ($data = $user->getById($session->getUserId())) !== null) {
            $visitor = new Visitor($session->getUserId(), $data);
        } else {
            $visitor = new Visitor;
        }
        $context->setVisitor($visitor);
        return $context;
    }
}
