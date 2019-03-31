<?php
namespace support\setting\response;

use suda\framework\Request;
use support\setting\Context;
use support\setting\Visitor;
use suda\application\Application;
use suda\application\template\ModuleTemplate;
use suda\application\processor\RequestProcessor;
use suda\framework\Response as FrameworkResponse;
use support\setting\controller\HistoryController;
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

    /**
     * 历史记录
     *
     * @var HistoryController
     */
    protected $history;

    public function onRequest(Application $application, Request $request, FrameworkResponse $response)
    {
        $this->context = (new ContextCreateProcessor)->onRequest($application, $request, $response);
        $this->history = new HistoryController;
        $response->setHeader('cache-control', 'no-store');
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
            $this->history->log($this->context->getSession()->id(), $request, $this->context->getVisitor()->getId());
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
     * @param array $parameter
     * @param boolean $allowQuery
     * @param string $default
     * @return void
     */
    public function goRoute(string $name, array $parameter = [], bool $allowQuery = true, ?string $default = null)
    {
        $url = $this->getUrl($name, $parameter, $allowQuery, $default);
        return $this->context->getResponse()->redirect($url);
    }

    /**
     * 获取URL
     *
     * @param string $name
     * @param array $parameter
     * @param boolean $allowQuery
     * @param string|null $default
     * @return string
     */
    public function getUrl(string $name, array $parameter = [], bool $allowQuery = true, ?string $default = null)
    {
        $default = $default ?: $this->context->getApplication()->getRunning()->getFullName();
        return $this->context->getApplication()->getUrl($this->context->getRequest(), $name, $parameter, $allowQuery, $default);
    }
    
    /**
     * 跳转到某页面
     *
     * @param string $url
     * @return void
     */
    public function redirect(string $url)
    {
        return $this->context->getResponse()->redirect($url);
    }
}
