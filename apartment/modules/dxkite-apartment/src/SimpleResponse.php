<?php

namespace dxkite\apartment\response;

use suda\framework\Request;
use suda\framework\Response;
use suda\application\Application;
use suda\application\processor\RequestProcessor;

class SimpleResponse implements RequestProcessor
{
    public function onRequest(Application $application, Request $request, Response $response)
    {
        $template = $application->getTemplate('support/setting:signin', $request);
        $template->set('ip', $request->getRemoteAddr());
        return $template;
    }
}
