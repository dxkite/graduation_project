<?php
namespace dxkite\openuser\provider;

use dxkite\openuser\controller\UserController;
use support\setting\provider\UserSessionAwareProvider;
use support\setting\Visitor;

class VisitorAwareProvider extends  UserSessionAwareProvider
{

    protected  $group = 'openuser';

    /**
     * 跳转到某路由
     *
     * @param string $name
     * @param array $parameter
     * @param bool $allowQuery
     * @param string|null $default
     * @return void
     */
    public function goRoute(string $name, array $parameter = [], bool $allowQuery = true, ?string $default = null)
    {
        $url = $this->getUrl($name, $parameter, $allowQuery, $default);
        $this->response->redirect($url);
    }

    /**
     * 获取URL
     *
     * @param string $name
     * @param array $parameter
     * @param boolean $allowQuery
     * @param string|null $default
     * @return string
     */
    public function getUrl(string $name, array $parameter = [], bool $allowQuery = true, ?string $default = null)
    {
        $default = $default ?: $this->application->getRunning()->getFullName();
        return $this->application->getUrl($this->request, $name, $parameter, $allowQuery, $default ?? $this->request->getAttribute('group'));
    }

    /**
     * @param string $userId
     * @return Visitor
     * @throws \suda\orm\exception\SQLException
     */
    public function createVisitor(string $userId)
    {
        $user = new UserController;
       if (($data = $user->getById($userId)) !== null) {
           return  new Visitor($userId, $data);
        } else {
            return new Visitor;
        }
    }
}
