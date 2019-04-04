<?php
namespace support\setting\response;

use suda\framework\Request;
use support\setting\MenuTree;
use suda\application\template\RawTemplate;
use support\setting\response\SignedResponse;
use support\openmethod\exception\PermissionException;

abstract class SettingResponse extends SignedResponse
{
    public function onAccessVisit(Request $request)
    {
        
        $visiter = $this->context->getVisitor();
        if ($visiter->canAccess([$this,'onSettingVisit'])) {
            try {
                $view = $this->onSettingVisit($request);
            } catch (PermissionException $e) {
                return $this->onDeny($request);
            }
            if ($view instanceof RawTemplate) {
                $menuTree = new MenuTree($this->context);
                $menu = $menuTree->getMenu($request->getAttribute('route'));
                $view->set('menuTree', $menu);
                foreach ($menu as $value) {
                    if ($value['select']) {
                        $view->set('title', $value['name']);
                        $view->set('menuName', $value['name']);
                        foreach ($value['child'] as $key => $submenu) {
                            if ($submenu['select']) {
                                $view->set('submenu', $submenu['name']);
                            }
                        }
                    }
                }
            }
            return $view;
        } else {
            $this->onDeny($request);
        }
    }

    abstract public function onSettingVisit(Request $request);
}
