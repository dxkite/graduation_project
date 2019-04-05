<?php
namespace dxkite\openuser\exception;

use suda\orm\TableStruct;
use support\setting\PageData;
use support\setting\UserSession;
use support\setting\VerifyImage;
use dxkite\openuser\table\UserTable;
use support\openmethod\parameter\File;
use dxkite\openuser\controller\UserController;
use support\setting\provider\UserSessionAwareProvider;

class UserProvider extends UserSessionAwareProvider
{
    /**
     * UserController
     *
     * @var UserController
     */
    protected $controller;

    public function __construct()
    {
        $this->controller = new UserController;
    }

    /**
     * 登陆
     *
     * @param string $account 账号
     * @param string $password 密码
     * @param string $code 验证码
     * @param boolean $remeber 记住登陆状态7天
     * @return \support\setting\UserSession 登陆会话
     */
    public function signin(string $account, string $password, string $code, bool $remeber = false): UserSession
    {
        $verify = new VerifyImage($this->context, 'dxkite/openuser');
        if ($verify->checkCode($code) === false) {
            throw new UserException('code error', UserException::ERR_CODE);
        }
        if ($user = $this->controller->signin($account, $password)) {
            $this->session = UserSession::save($user['id'], $this->request->getRemoteAddr(), $remeber ? 3600 : 25200, 'openuser');
            $this->context->getSession()->update();
        } else {
            throw new UserException('password or account error', UserException::ERR_PASSWORD_OR_ACCOUNT);
        }
        return $this->session;
    }

    /**
     * 退出登陆
     *
     * @param string $user
     * @return boolean
     */
    public function signout(string $user): bool
    {
        return UserSession::expire($user, 'openuser');
    }
    
    /**
     * 注册用户
     *
     * @param string $name
     * @param string $password
     * @param string $code
     * @param string|null $mobile
     * @param string|null $email
     * @param integer $status
     * @return \support\setting\UserSession
     */
    public function signup(string $name, string $password, string $code, ?string $mobile = null, ?string $email = null): UserSession
    {
        $verify = new VerifyImage($this->context, 'dxkite/openuser');
        if ($verify->checkCode($code) === false) {
            throw new UserException('code error', UserException::ERR_CODE);
        }
        $user = $this->controller->add($name, $password, $ip, $mobile, $email, UserTable::NORMAL);
        $this->session = UserSession::save($user, $this->request->getRemoteAddr(), $remeber ? 3600 : 25200, 'openuser');
        $this->context->getSession()->update();
        return $this->session;
    }

    /**
     * 编辑用户信息
     *
     * @param \support\openmethod\parameter\File $headimg
     * @param string $name
     * @param string $password
     * @param string|null $mobile
     * @param string|null $email
     * @return boolean
     */
    public function edit(File $headimg, string $name, ?string $mobile = null, ?string $email = null): bool
    {
        // TODO save file to protected
        return $this->controller->edit($this->visitor->getId(), $name, $headimg, $ip, $mobile, $email, $by, $status);
    }
    
    /**
     * 获取当前用户信息
     *
     * @return \suda\orm\TableStruct|null
     */
    public function getInfo():?TableStruct
    {
        return $this->controller->getInfoById($this->visitor->getId());
    }
}
