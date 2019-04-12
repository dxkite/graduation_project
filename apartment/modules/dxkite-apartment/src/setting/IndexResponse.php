<?php
namespace dxkite\apartment\response\setting;

use suda\framework\Request;
use dxkite\apartment\Setting;
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
        $setting = new Setting('apartment');
        $start = $setting->get('apartment_start_time', null);
        if ($start !== null) {
            $view->set('time_start', date('Y-m-d H:i:s', $start));
            $view->set('time_end', date('Y-m-d H:i:s', $setting->get('apartment_end_time')));
        }
        $min = $setting->get('apartment_min_pay');
        if ($min !== null) {
            $view->set('minpay', number_format($min / 100, 2, '.', ''));
        }
        $must = $setting->get('apartment_must_pay');
        if ($min !== null) {
            $view->set('mustpay', $must);
        }
        return $view;
    }
}
