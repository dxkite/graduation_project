<?php


namespace dxkite\openuser\response\reset;


use dxkite\openuser\exception\UserException;
use dxkite\openuser\provider\UserProvider;
use suda\framework\Request;
use suda\orm\exception\SQLException;

class IndexResponse extends Response
{

    function onVisit(Request $request)
    {
        $view = $this->application->getTemplate('reset/reset', $request);
        $type = $request->get('type');
        $view->set('title', '重置密码');
        $account = $request->get('account');
        if ($request->hasPost('password') && $request->hasPost('code') && $request->hasPost('humanCode')) {
            $provider = new UserProvider();
            $provider->loadFromContext($this->context);
            $password = $request->post('password');
            $repeat = $request->post('repeat');
            if ($password !== $repeat) {
                $view->set('passwordError', true);
                return $view;
            }
            try {
                $reset = $provider->resetPassword($type, $account, $password, $request->post('code'), $request->post('humanCode'));
                if ($reset) {
                    $view->set('resetSuccess', true);
                    $this->jumpForward();
                } else {
                    $view->set('resetError', true);
                }
            } catch (UserException $e) {
                switch ($e->getCode()) {
                    case UserException::ERR_SAME_PASSWORD:
                        $view->set('passwordConfirm', true);
                        break;
                    case UserException::ERR_CHECK_CODE:
                        $view->set('invalidCode', '安全验证码错误');
                        break;
                    case UserException::ERR_CODE:
                        $view->set('invalidHumanCode', '图片验证码错误');
                        break;
                }
            } catch (SQLException $e) {

            }
        }
        return $view;
    }
}