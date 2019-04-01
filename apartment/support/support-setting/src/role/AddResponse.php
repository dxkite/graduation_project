<?php
namespace support\setting\response\role;

use suda\framework\Request;
use support\openmethod\Permission;
use support\setting\response\SettingResponse;
use support\setting\controller\VisitorController;

class AddResponse extends SettingResponse
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
        if ($request->hasPost('name')) {
            $id = $controller->createRole($request->post('name'), new Permission(array_keys($request->post('auths', []))));
            if ($id) {
                $this->goRoute('role_list');
                return;
            } else {
                $view->set('invaildName', true);
            }
        }
        $view = $this->view('role/add');
        $auths = $this->context->getVisitor()->getPermission()->readPermissions($this->context->getApplication());
        $view->set('title', $this->_('添加角色'));
        $view->set('auths', $auths);
        return $view;
    }
}
