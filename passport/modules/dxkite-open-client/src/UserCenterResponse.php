<?php


namespace dxkite\openclient\response;


use dxkite\openclient\controller\UCConfigController;
use suda\framework\Request;
use support\setting\response\SettingResponse;

class UserCenterResponse extends SettingResponse
{
    public function onSettingVisit(Request $request)
    {
        $view = $this->application->getTemplate('user-center', $request);
        $ucCtr  = new UCConfigController();
        $view->set('config', $ucCtr->loadConfig($this->application->getDataPath()));
        if ($request->hasPost('server') && $request->hasPost('appid') && $request->hasPost('secret')) {
            $config = [
                'appid' => $request->post('appid'),
                'secret' => $request->post('secret'),
                'server' => $request->post('server')
            ];
            $ucCtr->saveConfig($this->application->getDataPath(), $config);
            $this->goRoute('user-center');
        }
        return $view;
    }
}