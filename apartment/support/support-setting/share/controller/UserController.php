<?php
namespace support\setting\controller;

use suda\orm\TableStruct;
use support\setting\table\UserTable;
use support\setting\exception\UserException;
use support\setting\controller\UserController;

class UserController
{
    /**
     * 用户表
     *
     * @var UserTable
     */
    protected $table;

    // 格式验证
    const EMAIL_PREG = '/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/';
    const MOBILE_PREG = '/^(13[0-9]|14[5-9]|15[012356789]|166|17[0-8]|18[0-9]|19[8-9])[0-9]{8}$/';
    const NAME_PREG = '/^[\w\x{4e00}-\x{9aff}]{4,255}$/u';

    public function __construct()
    {
        $this->table = new UserTable;
    }

    /**
     * 登陆
     *
     * @param string $account 账号
     * @param string $password 密码
     * @return null|TableStruct 能登陆则非空
     */
    public function signin(string $account, string $password): ?TableStruct
    {
        $user = $this->getByAccount($account);
        if ($user['status'] == UserTable::FREEZE) {
            throw new UserException('account is not active', UserException::ERR_ACCOUNT_IS_NOT_ACTIVE);
        }
        if (password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        return null;
    }
 
    /**
     * 添加用户
     *
     * @param string $name
     * @param string $password
     * @param string $ip
     * @param string|null $mobile
     * @param string|null $email
     * @param string|null $by
     * @param integer $status
     * @return boolean
     */
    public function add(string $name, string $password, string $ip, ?string $mobile = null, ?string $email = null, ?string $by = null, int $status = UserTable::NORMAL): bool
    {
        $this->assertName($name);
        $this->assertEmail($email);
        $this->assertMobile($mobile);
        return $this->table->write([
            'name' => $name,
            'password' => \password_hash($password, PASSWORD_DEFAULT),
            'mobile' => $mobile,
            'email' => $email,
            'create_ip' => $ip,
            'create_by' => $by,
            'create_time' => time(),
            'status' => $status,
        ])->isOk();
    }

    /**
     * 通过用户名获取用户
     *
     * @param string $name
     * @return array|null
     */
    public function getByName(string $name):?TableStruct
    {
        return $this->table->read('*')->where('LOWER(name)=LOWER(:name)', ['name' => $name])->one();
    }

    /**
     * 通过用户邮箱获取用户
     *
     * @param string $email
     * @return array|null
     */
    public function getByEmail(string $email):?TableStruct
    {
        return $this->table->read('*')->where('LOWER(email)=LOWER(:email)', ['email' => $email])->one();
    }

    /**
     * 通过手机号获取用户
     *
     * @param string $mobile
     * @return array|null
     */
    public function getByMobile(string $mobile):?TableStruct
    {
        return $this->table->read('*')->where(['mobile' => $email])->one();
    }

    public function getByAccount(string $account):TableStruct
    {
        if (preg_match(UserController::EMAIL_PREG, $account)) {
            return $this->getByEmail($account);
        } elseif (preg_match(UserController::MOBILE_PREG, $account)) {
            return $this->getByMobile($account);
        } else {
            return $this->getByName($account);
        }
        throw new UserException('account not exists', UserException::ERR_ACCOUNT_NOT_FOUND);
    }

    protected function assertMobile(?string $mobile)
    {
        $mobile = trim($mobile);
        if (strlen($mobile) > 0 && !preg_match(UserController::MOBILE_PREG, $mobile)) {
            throw new UserException('invalid user mobile', UserException::ERR_MOBILE_FORMAT);
        }
    }

    protected function assertName(string $name)
    {
        $name = trim($name);
        if (!preg_match(UserController::NAME_PREG, $name)) {
            throw new UserException('invalid user name', UserException::ERR_NAME_FORMAT);
        }
        return $name;
    }

    protected function assertEmail(?string $email)
    {
        $email = trim($email);
        if (strlen($email) > 0 && !preg_match(UserController::EMAIL_PREG, $email)) {
            throw new UserException('invalid user email', UserException::ERR_EMAIL_FORMAT);
        }
        return $email;
    }
}
