<?php


namespace dxkite\openuser\response\reset;



use dxkite\openuser\exception\UserException;
use dxkite\openuser\provider\UserProvider;
use suda\framework\Request;
use suda\database\exception\SQLException;

class ByEmailResponse extends Response
{
    /**
     * @param Request $request
     * @return \suda\application\template\ModuleTemplate|null
     */
    function onVisit(Request $request)
    {
        $view = $this->application->getTemplate('reset/email', $request);
        if ($request->hasPost('email') && $request->hasPost('code')) {
            $provider = new UserProvider();
            $provider->loadFromContext($this->context);
            try {
                $res = $provider->sendResetPasswordCode('email', $request->post('email'), $request->post('code'));
                if ($res) {
                    $this->goRoute('reset_password', ['type' => 'email', 'account' => $request->post('email'), 'redirect_uri' => $this->getRedirectUrl()]);
                    return null;
                }else{
                    $view->set('sendError',true);
                }
            } catch (UserException $e) {
                switch ($e->getCode()) {
                    case UserException::ERR_EMAIL_NOT_EXISTS:
                        $view->set('invalidEmail', '账号不存在');
                        break;
                    case UserException::ERR_EMAIL_NOT_CHECKED:
                        $view->set('invalidEmail', '账号未验证');
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