<?php
namespace support\setting\response;

use suda\framework\Request;
use suda\framework\Response as FrameworkResponse;
use support\setting\Context;
use support\setting\Visitor;
use suda\application\Application;
use suda\application\template\ModuleTemplate;
use suda\application\processor\RequestProcessor;
use support\setting\processor\ContextCreateProcessor;

abstract class Response implements RequestProcessor
{
    /**
     * 环境
     *
     * @var Context
     */
    protected $context;

    /**
     * 设置模板信息
     *
     * @var ModuleTemplate
     */
    protected $template;

    public function onRequest(Application $application, Request $request, FrameworkResponse $response)
    {
        $this->context = (new ContextCreateProcessor)->onRequest($application, $request, $response);
        $response->setHeader('cache-control','no-store');
        if ($this->context->getVisitor()->isGuest()) {
            return $this->onGuestVisit($request);
        } else {
            return $this->onUserVisit($request);
        }
    }

    abstract public function onGuestVisit(Request $request);
    abstract public function onAccessVisit(Request $request);

    public function onUserVisit(Request $request)
    {
        if ($this->context->getVisitor()->canAccess([$this, 'onAccessVisit'])) {
            return $this->onAccessVisit($request);
        } else {
            return $this->onDeny($request);
        }
    }

    public function onDeny(Request $request)
    {
        return $this->view('deny');
    }

    /**
     * 获取模板
     *
     * @param string $name
     * @return ModuleTemplate
     */
    public function view(string $name):ModuleTemplate
    {
        return $this->context->getApplication()->getTemplate($name, $this->context->getRequest());
    }

    /**
     * 跳转到某路由
     *
     * @param string $name
     * @return void
     */
    public function goRoute(string $name){
        $url = $this->context->getApplication()->getUrl($this->context->getRequest(), $name);
        return $this->context->getResponse()->redirect($url);
    }
}
