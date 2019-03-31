<?php
namespace support\setting\processor;

use suda\framework\Request;
use suda\framework\Response;
use support\setting\Context;
use support\setting\Visitor;
use suda\application\Application;
use suda\application\processor\RequestProcessor;

class ContextCreateProcessor implements RequestProcessor
{
    public function onRequest(Application $application, Request $request, Response $response)
    {
        $context = new Context($application, $request, $response);
        $session = $context->getSession();
        if ($session->has('visitor_user')) {
            $context->setVisitor($session->get('visitor_user'));
        }else{
            $context->setVisitor(new Visitor);
        }
        return $context;
    }
}
