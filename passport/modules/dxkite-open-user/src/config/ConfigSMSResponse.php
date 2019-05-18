<?php


namespace dxkite\openuser\response\config;

use dxkite\openuser\controller\ConfigController;
use suda\framework\Request;
use support\setting\response\SettingResponse;

class ConfigSMSResponse extends SettingResponse {

    public function onSettingVisit(Request $request)
    {
        $view = $this->application->getTemplate('config/tencent-sms', $request);

        $view->set('config', ConfigController::loadTencentSMSConfig($this->application->getDataPath()));
        if ($request->hasPost('app-id')
            && $request->hasPost('app-key')
            && $request->hasPost('sign')
            && $request->hasPost('template')) {
            $config = [
                'app-id' => $request->post('app-id'),
                'app-key' => $request->post('app-key'),
                'sign' => $request->post('sign'),
                'template' => $request->post('template')
            ];
            ConfigController::saveTencentSMSConfig($this->application->getDataPath(), $config);
            $this->goRoute('tencent-sms');
        }
        return $view;
    }
}