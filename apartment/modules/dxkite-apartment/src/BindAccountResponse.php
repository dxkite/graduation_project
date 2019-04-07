<?php
namespace dxkite\apartment\response;

/**
 * 绑定账户
 */
class BindAccountResponse extends BaseActionResponse
{
    public function onNotBind()
    {
        $view = $this->view('bind');
        $view->set('title', __('绑定账号'));
        return $view;
    }

    public function onClose()
    {
        $view = $this->view('bind');
        $view->set('title', __('系统关闭'));
        return $view;
    }
}
