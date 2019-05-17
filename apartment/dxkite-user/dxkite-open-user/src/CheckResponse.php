<?php


namespace dxkite\openuser\response;


use dxkite\openuser\provider\UserProvider;
use suda\framework\Request;

class CheckResponse extends UserSignResponse
{
    /**
     * @param Request $request
     * @return \suda\application\template\ModuleTemplate
     * @throws \suda\orm\exception\SQLException
     */
    public function onUserVisit(Request $request)
    {
        $view = $this->view('check');
        $view->set('title', '验证账号');
        if ($request->hasPost('code')) {
            $provider = new UserProvider;
            $provider->loadFromContext($this->context);
            $checked = $provider->check($request->post('code'));
            $view->set('invalidCode', $checked == false);
            if ($checked) {
                $this->jumpForward();
            }
        }
        return $view;
    }
}