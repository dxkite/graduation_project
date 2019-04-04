<?php
namespace support\setting\processor;

use suda\framework\Request;
use suda\framework\Response;
use support\setting\Context;
use support\setting\Visitor;
use support\setting\UserSession;
use suda\application\Application;
use support\setting\provider\VisitorProvider;
use suda\application\processor\RequestProcessor;

class ContextCreateProcessor implements RequestProcessor
{
    public function onRequest(Application $application, Request $request, Response $response)
    {
        $context = new Context($application, $request, $response);
        $session = UserSession::createParameterFromRequest(0, '', '', $application, $request);
        $vp = new VisitorProvider;
        $context->setVisitor($vp->getVisitor($session));
        return $context;
    }
}
