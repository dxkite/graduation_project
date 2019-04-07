<?php
namespace dxkite\apartment\response\setting;


use suda\framework\Request;
use support\openmethod\Permission;
use support\setting\provider\UserProvider;
use support\setting\exception\UserException;
use support\setting\provider\VisitorProvider;

class IndexResponse extends \support\setting\response\SettingResponse
{
    /**
     * 添加管理
     * 
     * @param Request $request
     * @return RawTemplate
     */
    public function onSettingVisit(Request $request)
    {
   
        $view = $this->view('setting/index');
        
        return $view;
    }
}
