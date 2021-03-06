<?php
namespace dxkite\openclient\controller;

use suda\database\struct\TableStruct;
use support\setting\PageData;
use dxkite\openclient\table\UserTable;
use suda\database\exception\SQLException;

class UserController
{
    /**
     * 用户表
     *
     * @var UserTable
     */
    protected $table;

    public function __construct()
    {
        $this->table = new UserTable;
    }

    /**
     * 链接到开放服务
     *
     * @param string $user
     * @param string $access_token
     * @param string $refresh_token
     * @param integer $expires_in
     * @param string $ip
     * @return string
     * @throws SQLException
     */
    public function signin(string $user, string $access_token, string $refresh_token, int $expires_in, string $ip = ''): string
    {
        if ($data = $this->table->read(['id'])->where(['user' => $user])->one()) {
            $this->table->write([
                'user' => $user,
                'access_token' => $access_token,
                'refresh_token' => $refresh_token,
                'expires_in' => $expires_in + time(),
            ]) -> where(['id' => $data['id']])->ok();
            return $data['id'];
        }
        return $this->table->write(
            [
                'user' => $user,
                'access_token' => $access_token,
                'refresh_token' => $refresh_token,
                'expires_in' => $expires_in + time(),
                'signup_time' => time(),
                'signup_ip' => $ip,
            ]
        )->id();
    }

    /**
     * 设置用户信息
     *
     * @param string $user
     * @param string $name
     * @param string $headimg
     * @return boolean
     * @throws SQLException
     */
    public function edit(string $user, string $name, string $headimg):bool
    {
        return $this->table->write([
            'name' => $name,
            'headimg' => $headimg,
        ])->where(['user' => $user])->ok();
    }

    /**
     * 检查是否需要数据
     *
     * @param string $id
     * @return boolean
     * @throws SQLException
     */
    public function wantUserInfo(string $user):bool
    {
        if ($data = $this->table->read(['name'])->where(['user' => $user])->one()) {
            return $data['name'] == null;
        }
        return false;
    }

    /**
     * 检查是否需要数据
     *
     * @param string $id
     * @return array|null
     * @throws SQLException
     */
    public function getInfoById(string $id):?array
    {
        if ($data = $this->table->read(['name','headimg'])->where(['id' => $id])->one()) {
            return $data;
        }
        return null;
    }

    /**
     * 检查是否需要数据
     *
     * @param string $id
     * @return array|null
     * @throws SQLException
     */
    public function getById(string $id):?array
    {
        if ($data = $this->table->read('*')->where(['id' => $id])->one()) {
            return $data;
        }
        return null;
    }
}
