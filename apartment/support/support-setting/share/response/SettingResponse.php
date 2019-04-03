<?php
namespace support\setting\response;

use suda\framework\Request;
use support\setting\MenuTree;
use suda\application\template\RawTemplate;
use support\setting\response\SignedResponse;

abstract class SettingResponse extends SignedResponse
{
    public function onAccessVisit(Request $request)
    {
        $menuTree = new MenuTree($this->context);
        
        $visiter = $this->context->getVisitor();
        if ($visiter->canAccess([$this,'onSettingVisit'])) {
            $view = $this->onSettingVisit($request);
            if ($view instanceof RawTemplate) {
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
