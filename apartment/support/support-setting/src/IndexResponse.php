<?php
namespace support\setting\response;

use suda\framework\Request;
use support\setting\response\SignedResponse;

class IndexResponse extends SignedResponse
{
    public function onAccessVisit(Request $request)
    {
        return $this->view('index');
    }
}
