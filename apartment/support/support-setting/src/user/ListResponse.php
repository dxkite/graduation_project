<?php
namespace support\setting\response\user;

use suda\framework\Request;
use support\openmethod\Permission;
use support\setting\provider\UserProvider;
use support\setting\response\SettingResponse;

class ListResponse extends SettingResponse
{
    /**
     * 列出权限
     *
     * @param Request $request
     * @return RawTemplate
     */
    public function onSettingVisit(Request $request)
    {
        $provider = new UserProvider;
        $provider->loadFromContext($this->context);
        $view = $this->view('user/list');
        $page = $request->get('page', 1);
        if ($request->hasGet('search')) {
            $list = $provider->search($request->get('search'), $page, 10);
            $view->set('search', $request->get('search'));
        } else {
            $list = $provider->list($page, 10);
        }
        $pageBar = $list->getPage();
        $pageBar['router'] = 'user_list';
        $view->set('title', $this->_('用户列表 第$0页', $list->getPageCurrent()));
        $view->set('list', $list->getRows());
        $view->set('page', $pageBar);
        return $view;
    }
}
