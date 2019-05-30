<?php

namespace dxkite\openuser\controller;

use support\setting\PageData;
use dxkite\openuser\sender\Send;
use suda\application\Application;
use dxkite\openuser\table\UserTable;
use suda\database\exception\SQLException;
use dxkite\openuser\exception\UserException;

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

    /**
     * 列表显示的数据
     *
     * @var array
     */
    protected $listFields = ['id', 'headimg', 'name', 'email', 'email_checked', 'mobile', 'mobile_checked', 'signup_time', 'signup_ip', 'status', 'code_type', 'code_expires'];

    public function __construct()
    {
        $this->table = new UserTable;
    }

    /**
     * 登陆
     *
     * @param string $account 账号
     * @param string $password 密码
     * @return null|array 能登陆则非空
     * @throws SQLException
     */
    public function signin(string $account, string $password): ?array
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
     * @param integer $status
     * @return string
     */
    public function add(string $name, string $password, string $ip = '', ?string $mobile = null, ?string $email = null, int $status = UserTable::NORMAL): string
    {
        $this->assertName($name);
        $this->assertEmail($email);
        $this->assertMobile($mobile);
        try {
            return $id = $this->table->write([
                'name' => $name,
                'password' => \password_hash($password, PASSWORD_DEFAULT),
                'mobile' => $mobile,
                'email' => $email,
                'signup_ip' => $ip,
                'signup_time' => time(),
                'status' => $status,
            ])->id();
        } catch (SQLException $e) {
            $message = $e->getMessage();
            if (strpos($message, 'name')) {
                throw new UserException('name exist error', UserException::ERR_NAME_EXISTS);
            }
            if (strpos($message, 'email')) {
                throw new UserException('email exist error', UserException::ERR_EMAIL_EXISTS);
            }
            if (strpos($message, 'mobile')) {
                throw new UserException('mobile exist error', UserException::ERR_MOBILE_EXISTS);
            }
            throw new UserException('account exist error', UserException::ERR_ACCOUNT_EXISTS);
        }
    }

    /**
     * 验证邮箱/短信验证码码
     *
     * @param string $user
     * @param string $code
     * @param integer $type
     * @return boolean
     * @throws SQLException
     */
    public function check(string $user, string $code): bool
    {
        if ($data = $this->table->read(['code', 'code_type', 'code_expires'])->where(['id' => $user])->one()) {
            if ($data['code'] !== $code || $data['code_type'] <= 0 || $data['code_expires'] < time()) {
                return false;
            }
            $write = ['code' => null, 'code_type' => 0, 'code_expires' => 0];
            if ($data['code_type'] == UserTable::CODE_CHECK_EMAIL) {
                $write['email_checked'] = 1;
            }
            if ($data['code_type'] == UserTable::CODE_CHECK_MOBILE) {
                $write['mobile_checked'] = 1;
            }
            return $this->table->write($write)->where(['id' => $user])->ok();
        }
        return true;
    }

    /**
     * @param string $user
     * @param string $code
     * @param int $type
     * @return bool
     * @throws SQLException
     */
    public function checkCode(string $user, string $code, int $type) {
        if ($data = $this->table->read(['code', 'code_type', 'code_expires'])->where(['id' => $user])->one()) {
            if ($data['code'] !== $code || $data['code_type'] <= 0 || $data['code_expires'] < time()) {
                return false;
            }
            if ($data['code_type'] != $type) {
                return false;
            }
        }
        return true;
    }

    /**
     * 重置验证码
     * @param string $user
     * @return bool
     * @throws SQLException
     */
    public function resetCheckCode(string $user) {
        $write = ['code' => null, 'code_type' => 0, 'code_expires' => 0];
        return $this->table->write($write)->where(['id' => $user])->ok();
    }

    /**
     * 编辑用户
     *
     * @param string $id
     * @param string $name
     * @param string $password
     * @param string|null $mobile
     * @param string|null $email
     * @param integer $status
     * @return boolean
     */
    public function edit(string $id, ?string $name = null, ?string $headimg = null, ?string $mobile = null, ?string $email = null): bool
    {
        $this->assertName($name);
        $this->assertEmail($email);
        $this->assertMobile($mobile);
        try {
            $data = [];
            if ($name !== null) {
                $data['name'] = $name;
            }
            if ($headimg !== null) {
                $data['headimg'] = $headimg;
            }
            if ($mobile !== null) {
                $data['mobile_checked'] = 0;
                $data['mobile'] = $mobile;
            }
            if ($email !== null) {
                $data['email_checked'] = 0;
                $data['email'] = $mobile;
            }
            if (count($data) > 0) {
                return $this->table->write($data)->where(['id' => $id])->ok();
            }
            return false;
        } catch (SQLException $e) {
            $message = $e->getMessage();
            if (strpos($message, 'name')) {
                throw new UserException('name exist error', UserException::ERR_NAME_EXISTS);
            }
            if (strpos($message, 'email')) {
                throw new UserException('email exist error', UserException::ERR_EMAIL_EXISTS);
            }
            if (strpos($message, 'mobile')) {
                throw new UserException('mobile exist error', UserException::ERR_MOBILE_EXISTS);
            }
            throw new UserException('account exist error', UserException::ERR_ACCOUNT_EXISTS);
        }
    }

    /**
     * 检查密码
     *
     * @param string $user
     * @param string $password
     * @return bool
     * @throws SQLException
     */
    public function checkPassword(string $user, string $password): bool
    {
        if ($data = $this->table->read(['password'])->where(['id' => $user])->one()) {
            if (password_verify($password, $data['password'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * 修改密码
     *
     * @param string $user
     * @param string $password
     * @return boolean
     * @throws SQLException
     */
    public function changePassword(string $user, string $password): bool
    {
        if ($data = $this->table->read(['password'])->where(['id' => $user])->one()) {
            if (password_verify($password, $data['password'])) {
                throw new UserException('password is same like old', UserException::ERR_SAME_PASSWORD);
            }
        }
        return $this->table->write([
            'password' => \password_hash($password, PASSWORD_DEFAULT),
        ])->where(['id' => $user])->ok();
    }

    /**
     * 通过用户名获取用户
     *
     * @param string $name
     * @return array|null
     * @throws SQLException
     */
    public function getByName(string $name): ?array
    {
        return $this->table->read('*')->where('LOWER(name)=LOWER(:name)', ['name' => $name])->one();
    }

    /**
     * 通过用户ID获取用户
     *
     * @param string $name
     * @return array|null
     * @throws SQLException
     */
    public function getById(string $id): ?array
    {
        return $this->table->read('*')->where('id = ?', $id)->one();
    }

    /**
     * 通过用户ID获取用户名和头像
     *
     * @param string $name
     * @return array|null
     * @throws SQLException
     */
    public function getBaseInfoById(string $id): ?array
    {
        return $this->table->read('id', 'name', 'headimg', 'signup_time')->where('id = ?', $id)->one();
    }

    /**
     * 通过用户ID获取用户信息
     *
     * @param string $id
     * @return array|null
     * @throws SQLException
     */
    public function getInfoById(string $id): ?array
    {
        return $this->table->read($this->listFields)->where('id = ?', $id)->one();
    }

    /**
     * 通过用户邮箱获取用户
     *
     * @param string $email
     * @return array|null
     * @throws SQLException
     */
    public function getByEmail(string $email): ?array
    {
        return $this->table->read('*')->where('LOWER(email)=LOWER(:email)', ['email' => $email])->one();
    }

    /**
     * 通过手机号获取用户
     *
     * @param string $mobile
     * @return array|null
     * @throws SQLException
     */
    public function getByMobile(string $mobile): ?array
    {
        return $this->table->read('*')->where(['mobile' => $mobile])->one();
    }

    /**
     * 获取账户
     *
     * @param string $account
     * @return array
     * @throws SQLException
     */
    public function getByAccount(string $account): array
    {
        if (preg_match(UserController::EMAIL_PREG, $account)) {
            $accountData = $this->getByEmail($account);
        } elseif (preg_match(UserController::MOBILE_PREG, $account)) {
            $accountData = $this->getByMobile($account);
        } else {
            $accountData = $this->getByName($account);
        }
        if ($accountData) {
            return $accountData;
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

    protected function assertName(?string $name)
    {
        $name = trim($name);
        if (strlen($name) > 0 && !preg_match(UserController::NAME_PREG, $name)) {
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

    /**
     * 列出用户
     *
     * @param integer|null $page
     * @param integer $row
     * @return PageData
     */
    public function list(?int $page = null, int $row = 10): PageData
    {
        return PageData::create($this->table->read($this->listFields), $page, $row);
    }

    /**
     * 搜索用户
     *
     * @param integer|null $page
     * @param integer $row
     * @return PageData
     * @throws SQLException
     */
    public function search(string $data, ?int $page = null, int $row = 10): PageData
    {
        return PageData::create($this->table->read($this->listFields)->where(' `name` LIKE CONCAT("%",:data,"%") OR `email` LIKE CONCAT("%",:data,"%") OR `mobile` LIKE CONCAT("%",:data,"%") ', ['data' => $data]), $page, $row);
    }

    /**
     * 设置状态
     *
     * @param string $user
     * @param integer $status
     * @return boolean
     * @throws SQLException
     */
    public function status(string $user, int $status): bool
    {
        return $this->table->write(['status' => $status])->where(['id' => $user])->ok();
    }

    /**
     * 删除用户
     *
     * @param string $user
     * @return boolean
     * @throws SQLException
     */
    public function delete(string $user): bool
    {
        return $this->table->delete(['id' => $user])->ok();
    }

    /**
     * @param Application $application
     * @param string $action
     * @param string $mobile
     * @param string $code
     * @param int $expires
     * @param int $expireIn
     * @param int $type
     * @param string|null $user
     * @param bool $checked
     * @return bool
     * @throws SQLException
     */
    public function sendMobileCode(Application $application, string $action, string $mobile, string $code, int $expires, int $expireIn, int $type, ?string $user = null, bool $checked = false)
    {
        if ($data = $this->table->read(['id', 'mobile_send', 'mobile_checked'])->where(['mobile' => $mobile])->one()) {
            if ($user !== null && $data['id'] != $user) {
                throw new UserException('mobile not exist for user', UserException::ERR_MOBILE_OWN);
            }
            if ($checked && $data['mobile_checked'] != 1) {
                throw new UserException('mobile not checked', UserException::ERR_MOBILE_NOT_CHECKED);
            }
            if (time() - $data['mobile_send'] > 60) {
                if (Send::sortMessage($application, $action, $mobile, $code, $expireIn)) {
                    return $this->table->write([
                        'code' => $code,
                        'code_type' => $type,
                        'code_expires' => $expires,
                    ])->where(['id' => $data['id']])->ok();
                }
                return false;
            }
            throw new UserException('mobile code frequency 1/min', UserException::ERR_CODE_FREQUENCY);
        }
        throw new UserException('mobile not exist', UserException::ERR_MOBILE_NOT_EXISTS);
    }

    /**
     * @param Application $application
     * @param string $action
     * @param string $email
     * @param string $code
     * @param int $expires
     * @param int $expireIn
     * @param int $type
     * @param string|null $user
     * @param bool $checked
     * @return bool
     * @throws SQLException
     */
    public function sendEmailCode(Application $application, string $action, string $email, string $code, int $expires, int $expireIn, int $type, ?string $user = null, bool $checked = false)
    {
        if ($data = $this->table->read(['id', 'email_checked'])->where(['email' => $email])->one()) {
            if ($user !== null && $data['id'] != $user) {
                throw new UserException('email not exist for user', UserException::ERR_EMAIL_OWN);
            }
            if ($checked && $data['email_checked'] != 1) {
                throw new UserException('email not checked', UserException::ERR_EMAIL_NOT_CHECKED);
            }
            if (Send::mail($application, $action, $email, $code, $expireIn)) {
                return $this->table->write([
                    'code' => $code,
                    'code_type' => $type,
                    'code_expires' => $expires,
                ])->where(['id' => $data['id']])->ok();
            }
            return false;
        }
        throw new UserException('mobile not exist', UserException::ERR_MOBILE_NOT_EXISTS);
    }
}
