<?php
namespace dxkite\openuser\provider;

use suda\orm\struct\TableStruct;
use support\setting\PageData;
use support\setting\provider\VisitorProvider;
use support\upload\UploadUtil;
use support\session\UserSession;
use support\setting\VerifyImage;
use dxkite\openuser\table\UserTable;
use support\openmethod\parameter\File;
use dxkite\openuser\exception\UserException;
use dxkite\openuser\controller\UserController;

class UserProvider extends VisitorAwareProvider
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
     * @return \support\session\UserSession 登陆会话
     * @throws \suda\orm\exception\SQLException
     */
    public function signin(string $account, string $password, string $code, bool $remeber = false): UserSession
    {
        $verify = new VerifyImage($this->context, 'dxkite/openuser');
        if ($verify->checkCode($code) === false) {
            throw new UserException('code error', UserException::ERR_CODE);
        }
        if ($user = $this->controller->signin($account, $password)) {
            $this->session = UserSession::save($user['id'], $this->request->getRemoteAddr(), $remeber ? 3600 : 25200, $this->group);
            $this->visitor = $this->createVisitor($this->session->getUserId());
            $this->context->update($this->visitor);
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
        return UserSession::expire($user, $this->group);
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
     * @return \support\session\UserSession
     * @throws \suda\orm\exception\SQLException
     */
    public function signup(string $name, string $password, string $code, ?string $mobile = null, ?string $email = null): UserSession
    {
        $verify = new VerifyImage($this->context, 'dxkite/openuser');
        if ($verify->checkCode($code) === false) {
            //  throw new UserException('code error', UserException::ERR_CODE);
        }
        $user = $this->controller->add($name, $password, $this->request->getRemoteAddr(), $email, $mobile, UserTable::NORMAL);
        $this->session = UserSession::save($user, $this->request->getRemoteAddr(), 3600, $this->group);
        $this->visitor = $this->createVisitor($this->session->getUserId());
        $this->context->update($this->visitor);
        return $this->session;
    }

    /**
     * 账号验证
     *
     * @param string $code
     * @return bool
     * @throws \suda\orm\exception\SQLException
     */
    public function check(string $code)
    {
        $codeType = $this->visitor->getAttribute('code_type', 0);
        if ($codeType > 0) {
            return $this->controller->check($this->visitor->getId(), $code);
        }
        return true;
    }

    /**
     * @param string $type
     * @return bool
     * @throws \suda\orm\exception\SQLException
     */
    public function sendCheckCode(string $type)
    {
        $code = mt_rand(100000, 999999);
        if ($type == 'email') {
            $email = $this->visitor->getAttribute('email');
            return $this->controller->sendEmailCode($this->application, '验证邮箱', $email, $code, time() + 300, 5, UserTable::CODE_EMAIL, $this->visitor->getId());
        }
        $mobile = $this->visitor->getAttribute('mobile');
        return $this->controller->sendMobileCode($this->application, '验证手机号', $mobile, $code, time() + 300, 5, UserTable::CODE_MOBILE, $this->visitor->getId());
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
    public function edit(?File $headimg, ?string $name, ?string $mobile = null, ?string $email = null): bool
    {
        if ($headimg !== null) {
            $path = UploadUtil::save($headimg);
        } else {
            $path = null;
        }
        return $this->controller->edit($this->visitor->getId(), $name, $path, $mobile, $email);
    }
    
    /**
     * 修改密码
     *
     * @param string $oldpassword
     * @param string $password
     * @return boolean
     */
    public function password(string $oldpassword, string $password):bool
    {
        $user = $this->visitor->getId();
        if ($this->controller->checkPassword($user, $oldpassword) === false) {
            throw new UserException('password error', UserException::ERR_PASSWORD_OR_ACCOUNT);
        }
        return $this->controller->changePassword($user, $password);
    }

    /**
     * 获取当前用户信息
     *
     * @return array|null
     */
    public function getInfo():?array
    {
        $data = $this->controller->getInfoById($this->visitor->getId());
        if ($data['headimg'] === null) {
            $data['headimg'] = '/upload/'.$data['headimg'];
        }
        return $data;
    }
}
