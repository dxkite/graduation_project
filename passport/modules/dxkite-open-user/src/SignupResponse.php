<?php
namespace dxkite\openuser\response;

use suda\framework\Request;
use suda\database\exception\SQLException;
use support\setting\response\Response;
use dxkite\openuser\provider\UserProvider;
use dxkite\openuser\response\UserResponse;
use dxkite\openuser\exception\UserException;


class SignupResponse extends UserResponse
{
    public function onGuestVisit(Request $request)
    {
        $view = $this->view('signup');
        $type = $request->get('type', 'mobile');
        $view->set('type', $type);
        if ($request->hasPost()) {
            $name = $request->post('name');
            $mobile = $request->post('mobile');
            $email = $request->post('email');
            $password = $request->post('password');
            $repeat = $request->post('repeat');
            $code = $request->post('code');

            $view->set('name', $name);
            $view->set('email', $email);
            $view->set('mobile', $mobile);
            
            $email = empty($email)?null:$email;
            $mobile = empty($mobile)?null:$mobile;

            if ($name && $password == $repeat) {
                if ($type == 'email') {
                    if (empty($email)) {
                        $view->set('checkField', true);
                    }
                } else {
                    if (empty($mobile)) {
                        $view->set('checkField', true);
                    }
                }
                if ($view->get('checkField', false) != true) {
                    try {
                        $p = new UserProvider;
                        $p->loadFromContext($this->context);
                        $session = $p->signup($name, $password, $code, $email, $mobile);
                        $session->processor($this->application, $this->request, $this->response);
                        try {
                            if ($p->sendCheckCode($type)) {
                                $this->goRoute('check', ['redirect_uri' => $this->getRedirectUrl()]);
                            }else{
                                $this->jumpForward();
                            }
                        } catch (SQLException $e) {
                            $this->jumpForward();
                            $this->application->debug()->error($e->getMessage(), ['exception' => $e]);
                        }
                    } catch (UserException $e) {
                        switch ($e->getCode()) {
                            case UserException::ERR_NAME_FORMAT:
                            $view->set('invalidName', '用户名格式错误');
                            break;
                            case UserException::ERR_NAME_EXISTS:
                            $view->set('invalidName', '用户名已存在');
                            break;
                            case UserException::ERR_EMAIL_FORMAT:
                            $view->set('invalidEmail', '邮箱格式错误');
                            break;
                            case UserException::ERR_EMAIL_EXISTS:
                            $view->set('invalidEmail', '邮箱已存在');
                            break;
                            case UserException::ERR_MOBILE_FORMAT:
                            $view->set('invalidMobile', '手机号格式错误');
                            break;
                            case UserException::ERR_MOBILE_EXISTS:
                            $view->set('invalidMobile', '手机号已经被注册');
                            break;
                            case UserException::ERR_CODE:
                            $view->set('invalidCode', '验证码错误');
                            break;
                        }
                    }
                }
            } else {
                $view->set('passwordError', true);
            }
        }
        return $view;
    }
    
    public function onUserVisit(Request $request)
    {
        $this->jumpForward();
    }
}
