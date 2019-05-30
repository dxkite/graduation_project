<?php
namespace dxkite\apartment\controller;

use suda\database\struct\TableStruct;
use dxkite\apartment\Setting;
use dxkite\apartment\table\StudentTable;

class UserController
{
    protected $table;

    public function __construct()
    {
        $this->table = new StudentTable;
    }

    /**
     * 检查是否绑定
     *
     * @param string $user
     * @return boolean
     * @throws \suda\database\exception\SQLException
     */
    public function isBinded(string $user):bool
    {
        return $this->getByUser($user) ?true:false;
    }

    /**
     * 绑定用户
     *
     * @param string $user
     * @param string $id
     * @return boolean
     * @throws \suda\database\exception\SQLException
     */
    public function bind(string $user, string $id):bool {
        return $this->table->write([
            'user' => $user,
        ])->where(['id'  => $id])->ok();
    }

    /**
     * 检查用户是否可选
     *
     * @param string $user
     * @return bool
     * @throws \suda\database\exception\SQLException
     */
    public function selectable(string $user):bool
    {
        $setting = new Setting('apartment');
        $data = $this->getByUser($user);
        // 欠费金额
        if ($data['arrearage'] <= $setting->get('apartment_min_pay', 0)) {
            if ($setting->get('apartment_must_pay') && $data['arrearage'] != 0) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 通过ID获取用户
     * @param int $user
     * @return array|null
     * @throws \suda\database\exception\SQLException
     */
    public function getByUser(string $user):?array
    {
        return $this->table->read('*')->where(['user' => $user])->one();
    }

    /**
     * @param string $id
     * @return mixed
     * @throws \suda\database\exception\SQLException
     */
    public function getById(string $id) {
        return $this->table->read('*')->where(['id' => $id])->one();
    }

    /**
     * 通过身份证获取用户
     * @param string $idcard
     * @return array|null
     * @throws \suda\database\exception\SQLException
     */
    public function getByIdCard(string $idcard):?array
    {
        return $this->table->read('*')->where(['idcard' => strtoupper($idcard) ])->one();
    }
}
