<?php
namespace support\setting\response\role;

use suda\framework\Request;
use support\openmethod\Permission;
use support\setting\provider\VisitorProvider;
use support\setting\response\SettingResponse;


 
class ListResponse extends  SettingResponse
{
    /**
     * 列出权限
     *
     * @param Request $request
     * @return RawTemplate
     */
    public function onSettingVisit(Request $request)
    {
        $controller = new VisitorProvider;
        $canDelete = $this->context->getVisitor()->hasPermission('role.delete');
        if ($request->hasGet('delete')) {
            $controller->deleteRole($request->get('delete'));
            $this->goBack($this->getUrl('role_list'));
            return;
        }
        $view = $this->view('role/list');
        $page = $request->get('page', 1);
        $list = $controller->listRole($page, 10);
        $pageBar = $list->getPage();
        $pageBar['router'] = 'role_list';
        $view->set('title', $this->_('角色列表 第$0页', $list->getPageCurrent()));
        $view->set('list', $list->getRows());
        $view->set('page', $pageBar);
        return $view;
    }
}
