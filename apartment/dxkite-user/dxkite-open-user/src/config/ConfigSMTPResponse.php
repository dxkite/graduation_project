<?php


namespace dxkite\openuser\response\config;

use dxkite\openuser\controller\ConfigController;
use suda\framework\Request;
use support\setting\response\SettingResponse;

class ConfigSMTPResponse extends SettingResponse {

    public function onSettingVisit(Request $request)
    {
        $view = $this->application->getTemplate('config/smtp', $request);

        $view->set('config', ConfigController::loadSMTPConfig($this->application->getDataPath()));
        if ($request->hasPost('server')
            && $request->hasPost('name')
            && $request->hasPost('security')
            && $request->hasPost('timeout')) {
            $config = [
                'server' => $request->post('server'),
                'name' => $request->post('name'),
                'security' => $request->post('security'),
                'timeout' => $request->post('timeout')
            ];
            if (strlen($request->post('password'))) {
                $config['password'] = $request->post('password');
            }
            ConfigController::saveSTMPConfig($this->application->getDataPath(), $config);
            $this->goRoute('smtp');
        }
        return $view;
    }
}