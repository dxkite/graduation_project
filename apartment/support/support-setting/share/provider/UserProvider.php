<?php
namespace support\setting\provider;

use support\setting\PageData;
use support\setting\UserSession;
use support\setting\VerifyImage;
use support\openmethod\parameter\File;
use support\setting\exception\UserException;
use support\setting\controller\UserController;

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
        $verify = new VerifyImage($this->context, 'support/setting');
        if ($verify->checkCode($code) === false) {
            throw new UserException('code error', UserException::ERR_CODE);
        }
        if ($user = $this->controller->signin($account, $password)) {
            $this->session = UserSession::save($user['id'], $this->request->getRemoteAddr(), $remeber ? 3600 : 25200);
        } else {
            throw new UserException('password or account error', UserException::ERR_PASSWORD_OR_ACCOUNT);
        }
        return $this->session;
    }
    
    public function add(File $image, string $name, string $password, ?string $mobile, ?string $email)
    {
    }

    /**
     * 列出用户
     *
     * @param integer|null $page
     * @param integer $row
     * @return PageData
     */
    public function list(?int $page = null, int $row = 10): PageData
    {
        return $this->controller->list($page, $row);
    }

    /**
     * 搜索用户
     *
     * @param string $data
     * @param integer|null $page
     * @param integer $row
     * @return \support\setting\PageData
     */
    public function search(string $data, ?int $page = null, int $row = 10): PageData
    {
        return $this->controller->search($data, $page, $row);
    }
}
