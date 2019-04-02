<?php
namespace support\setting\response\role;

use suda\framework\Request;
use support\openmethod\Permission;
use support\setting\response\SettingResponse;
use support\setting\controller\VisitorController;

class EditResponse extends SettingResponse
{
    /**
     * 列出权限
     *
     * @param Request $request
     * @return RawTemplate
     */
    public function onSettingVisit(Request $request)
    {
        $controller = new VisitorController;
        $id = $request->get('id');
        $role = $controller->getRole($id);
        $view = $this->view('role/edit');
        if ($role !== null) {
            if ($request->hasPost('auths')) {
                $controller->editRole($id, $request->post('name'), new Permission(array_keys($request->post('auths', []))));
                $role = $controller->getRole($id);
            }
            $view->set('title', $this->_('编辑角色'));
            $auths = $this->visitor->getPermission()->readPermissions($this->application);
            $view->set('auths', $auths);
            $view->set('permission', $role['permission']);
            $view->set('name', $role['name']);
        } else {
            $view->set('invaildId', true);
        }
        return $view;
    }
}
