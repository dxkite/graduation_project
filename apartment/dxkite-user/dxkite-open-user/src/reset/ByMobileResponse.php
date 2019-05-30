<?php


namespace dxkite\openuser\response\reset;


use dxkite\openuser\exception\UserException;
use dxkite\openuser\provider\UserProvider;
use suda\framework\Request;
use suda\database\exception\SQLException;

class ByMobileResponse extends Response
{

    function onVisit(Request $request)
    {
        $view = $this->application->getTemplate('reset/mobile', $request);
        if ($request->hasPost('mobile') && $request->hasPost('code')) {
            $provider = new UserProvider();
            $provider->loadFromContext($this->context);
            try {
                $res = $provider->sendResetPasswordCode('mobile', $request->post('mobile'), $request->post('code'));
                if ($res) {
                    $this->goRoute('reset_password', ['type' => 'mobile', 'account' => $request->post('mobile'), 'redirect_uri' => $this->getRedirectUrl()]);
                    return null;
                }else{
                    $view->set('sendError',true);
                }
            } catch (UserException $e) {
                switch ($e->getCode()) {
                    case UserException::ERR_MOBILE_NOT_EXISTS:
                        $view->set('invalidMobile', '账号不存在');
                        break;
                    case UserException::ERR_MOBILE_NOT_CHECKED:
                        $view->set('invalidMobile', '账号未验证');
                        break;
                    case UserException::ERR_CODE:
                        $view->set('invalidCode', '验证码错误');
                        break;
                }
            } catch (SQLException $e) {

            }
        }
        return $view;
    }
}