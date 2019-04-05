<?php
namespace support\setting\provider;

use suda\orm\TableStruct;
use support\setting\PageData;
use support\setting\UserSession;
use support\setting\VerifyImage;
use support\setting\table\UserTable;
use support\setting\exception\UserException;
use support\setting\controller\UserController;
use support\setting\controller\VisitorController;

class SettingBaseProvider extends UserSessionAwareProvider
{
    protected $group = 'system';
}
