<?php
namespace support\setting\provider;

use support\setting\UserSession;
use support\setting\exception\UserException;
use support\setting\controller\UserController;

class UserProvider extends UserSessionAwareProvider
{

    /**
     * 登陆
     *
     * @param string $account 账号
     * @param string $password 密码
     * @param boolean $remeber 记住登陆状态7天
     * @return \support\setting\UserSession 登陆会话
     */
    public function signin(string $account, string $password, bool $remeber = false): UserSession
    {
        $controller = new UserController;
        if ($user = $controller->signin($account, $password)) {
            $this->session = UserSession::save($user['id'], $this->request->getRemoteAddr(), $remeber ? 3600 : 25200);
        } else {
            throw new UserException('password or account error', UserException::ERR_PASSWORD_OR_ACCOUNT);
        }
        return $this->session;
    }
}
