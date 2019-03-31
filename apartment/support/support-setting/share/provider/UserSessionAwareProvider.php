<?php
namespace support\setting\provider;

use suda\framework\Request;
use suda\framework\Response;
use support\setting\UserSession;
use suda\application\Application;
use support\setting\exception\UserException;
use support\openmethod\FrameworkContextAwareTrait;
use support\openmethod\FrameworkContextAwareInterface;

class UserSessionAwareProvider implements FrameworkContextAwareInterface
{
    use FrameworkContextAwareTrait {
        setContext as setBaseContext;
    }
    
    /**
     * 用户会话
     *
     * @var UserSession
     */
    protected $session;

    /**
     * 环境感知
     *
     * @param \suda\application\Application $application
     * @param \suda\framework\Request $request
     * @param \suda\framework\Response $response
     * @return void
     */
    public function setContext(Application $application, Request $request, Response $response)
    {
        $this->setBaseContext($application, $request, $response);
        $this->session = UserSession::createParameterFromRequest(0, '', '', $application, $request);
    }
}