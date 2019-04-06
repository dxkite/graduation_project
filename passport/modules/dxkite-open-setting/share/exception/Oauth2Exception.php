<?php
namespace dxkite\openuser\setting\exception;

class Oauth2Exception extends \Exception
{
    const ERR_SYSTEM = 50000;
    const ERR_APPID = 50001;
    const ERR_CODE = 50002;
    const ERR_ACCESS_TOKEN = 50003;
    const ERR_REFRESH_TOKEN = 50004;
    const ERR_USER = 50005;
}
