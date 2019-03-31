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

    public function loadPermission(string $userId) {
        $grantName = $this->grant->getName();
        $roleName = $this->role->getName();
        $permissions = $this->role->query("SELECT permission FROM _:{$roleName} JOIN  _:{$grantName} ON _:{$grantName}.grant = _:{$roleName}.id WHERE grantee = ? ", $userId)->all();
        if ($permissions) {
            $permission=new Permission;
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
}
