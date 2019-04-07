<?php
namespace dxkite\apartment\response;

use dxkite\apartment\Setting;
use dxkite\apartment\controller\UserController;
use dxkite\apartment\response\BaseActionResponse;
use dxkite\apartment\controller\ApartmentController;

/**
 * 绑定账户
 */
class HomeResponse extends BaseActionResponse
{
    public function onBinded()
    {
        return $this->home();
    }

    public function onClose()
    {
        return $this->home();
    }

    public function home()
    {
        $view = $this->view('home');
        $userId = $this->visitor->getId();
        $user = (new UserController)->getByUser($userId);
        $view->set('welcome', __('$0 $1专业', $user['name'], $user['major']));
        if ($user['sex'] == '男') {
            $view->set('hello', __('$0专业的学弟你好', $user['major']));
        } else {
            $view->set('hello', __('$0专业的学妹你好', $user['major']));
        }
        $ctr = new ApartmentController;
        if (!$ctr->isClose()) {
            $view->set('open', true);
        }
        if ((new UserController)->selectable($userId)) {
            $view->set('selectable', true);
        }
        $data = $ctr->get($userId);
        if ($data) {
            $view->set('selected', $data);
        }
        if ($ctr->isClose()) {
            $view->set('close', true);
            $setting = new Setting('apartment');
            $start = date('Y-m-d H:i:s', $setting->get('apartment_start_time'));
            $end = date('Y-m-d H:i:s', $setting->get('apartment_end_time'));
            $view->set('time', __('$0 到 $1', $start, $end));
        }
        $view->set('title', __('$0 - $1 涉外学院宿舍选择系统', $user['name'], $user['major']));
        return $view;
    }
}
