<?php
namespace support\setting\controller;

use suda\orm\TableStruct;
use support\openmethod\Permission;
use support\setting\table\RoleTable;
use support\setting\table\GrantTable;

class VisitorController
{
    /**
     * 授权表
     *
     * @var GrantTable
     */
    protected $grant;
    /**
     * 角色表
     *
     * @var RoleTable
     */
    protected $role;
 
    public function __construct()
    {
        $this->grant = new GrantTable;
        $this->role = new RoleTable;
    }

    /**
     * 加载用户权限
     *
     * @param string $userId
     * @return \support\openmethod\Permission
     */
    public function loadPermission(string $userId):Permission
    {
        $grantName = $this->grant->getName();
        $roleName = $this->role->getName();
        $permissions = $this->role->query("SELECT permission FROM _:{$roleName} JOIN  _:{$grantName} ON _:{$grantName}.grant = _:{$roleName}.id WHERE grantee = ? ", $userId)->all();
        if ($permissions) {
            $permission = new Permission;
            foreach ($permissions as $item) {
                if ($item['permission'] instanceof Permission) {
                    $permission->merge($item['permission']);
                }
            }
            return $permission;
        } else {
            return new Permission;
        }
    }

    /**
     * 创建权限角色
     *
     * @param string $name 角色名
     * @param \support\openmethod\Permission $permission 权限
     * @param integer $sort 排序
     * @return int 角色ID
     */
    public function createRole(string $name, Permission $permission, int $sort = 0):int
    {
        if ($data = $this->role->read('id')->where(['name' => $name])->one()) {
            return $data['id'];
        }
        return $this->role->write(['name' => $name,'permission' => $permission,'sort' => $sort])->id();
    }

    /**
     * 编辑角色
     *
     * @param integer $id
     * @param string $name
     * @param \support\openmethod\Permission $permisson
     * @param integer $sort
     * @return boolean
     */
    public function editRole(int $id, string $name, Permission $permisson, int $sort = 0): bool
    {
        return $this->role->write([
            'name' => $name,
            'permission' => $permisson,
            'sort' => $sort,
        ])->where(['id' => $id]) -> ok();
    }
    
    /**
     * 删除角色
     *
     * @param integer $id
     * @return boolean
     */
    public function deleteRole(int $id):bool
    {
        return $this->role->delete(['id' => $id])->ok();
    }

    /**
     * 授权
     *
     * @param integer $id 角色ID
     * @param string $grantee 权限所有者
     * @param string $investor 授权者
     * @return boolean
     */
    public function grant(int $id, string $grantee, string $investor = null): bool
    {
        if ($this->grant->read('id')->where(['grantee' => $grantee,'grant' => $id])->one()) {
            return true;
        }
        return $this->role->write(['investor' => $investor,'grantee' => $grantee,'time' => time(),'grant' => $id])->ok();
    }

    /**
     * 收回权限
     *
     * @param integer $id
     * @param integer $grantee
     * @return boolean
     */
    public function revoke(int $id, int $grantee): bool
    {
        if ($data = $this->grant->read('id')->where(['grantee' => $grantee,'grant' => $id])->one()) {
            return  $this->grant->delete(['id' => $data])->ok();
        }
        return false;
    }
 
    /**
     * 收回某个用户的全部权限
     *
     * @param integer $grantee
     * @return boolean
     */
    public function revokeAll(int $grantee):bool
    {
        return $this->grant->delete(['grantee' => $grantee])->ok();
    }
}
