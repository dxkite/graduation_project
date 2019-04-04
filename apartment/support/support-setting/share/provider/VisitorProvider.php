<?php
namespace support\setting\provider;

use suda\orm\TableStruct;
use support\setting\Visitor;
use support\setting\PageData;
use support\setting\UserSession;
use support\openmethod\Permission;
use support\setting\exception\UserException;
use support\setting\controller\UserController;
use support\setting\controller\VisitorController;

class VisitorProvider extends UserSessionAwareProvider
{
    /**
     * VisitorController
     *
     * @var VisitorController
     */
    protected $controller;

    public function __construct()
    {
        $this->controller = new VisitorController;
    }

    /**
     * 获取用户
     *
     * @param \support\setting\UserSession $session
     * @return Visitor
     */
    public function getVisitor(UserSession $session):Visitor
    {
        return  $this->controller->getVisitor($session);
    }

   

    /**
     * 创建权限角色
     *
     * @param string $name 角色名
     * @param array $permission 权限
     * @param integer $sort 排序
     * @return integer 角色ID
     */
    public function createRole(string $name, array $permission, int $sort = 0):int
    {
        return $this->controller->createRole($name, new Permission($permission), $sort);
    }

    /**
     * 编辑角色
     *
     * @param integer $id
     * @param string $name
     * @param array $permission
     * @param integer $sort
     * @return boolean
     */
    public function editRole(int $id, string $name, array $permission, int $sort = 0): bool
    {
        return $this->controller->editRole($id, $name, new Permission($permission), $sort);
    }
    
    /**
     * 删除角色
     *
     * @param integer $id
     * @return boolean
     */
    public function deleteRole(int $id):bool
    {
        return $this->controller->deleteRole($id);
    }

    /**
     * 获取
     *
     * @param integer $id
     * @return TableStruct|null
     */
    public function getRole(int $id):?TableStruct
    {
        return $this->controller->getRole($id);
    }

    /**
     * 授权
     *
     * @param integer $id 角色ID
     * @param string $grantee 权限所有者
     * @return boolean
     */
    public function grant(int $id, string $grantee): bool
    {
        return $this->controller->grant($id, $grantee, $this->context->getVisitor()->getId());
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
        return $this->controller->revoke($id, $grantee);
    }
 
    /**
     * 收回某个用户的全部权限
     *
     * @param integer $grantee
     * @return boolean
     */
    public function revokeAll(int $grantee):bool
    {
        return $this->controller->revokeAll($grantee);
    }


    /**
     * 列出角色列表
     *
     * @param integer|null $page
     * @param integer $row
     * @return PageData
     */
    public function listRole(?int $page = null, int $row = 10): PageData
    {
        return $this->controller->listRole($page, $row);
    }

    /**
     * 列出角色列表
     *
     * @param string $user
     * @param integer|null $page
     * @param integer $row
     * @return \support\setting\PageData
     */
    public function listUserRole(string $user, ?int $page = null, int $row = 10): PageData
    {
        return $this->controller->listUserRole($user, $page, $row);
    }
}
