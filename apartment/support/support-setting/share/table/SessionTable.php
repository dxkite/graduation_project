<?php
namespace support\setting\table;

use suda\orm\TableStruct;
use support\setting\table\AutoCreate;

/**
 * 登陆日志
 */
class SessionTable extends AutoCreate
{
    public function __construct()
    {
        parent::__construct('setting_session');
    }

    public function onCreateStruct(TableStruct $struct):TableStruct
    {
        return $struct->fields([
            $struct->field('id', 'bigint', 20)->primary()->unsigned()->auto(),
            $struct->field('grantee', 'bigint', 20)->key()->comment('会话所有者'),
            $struct->field('group', 'bigint', 20)->comment('会话分组'),
            $struct->field('token', 'varchar', 32)->comment('验证令牌'),
            $struct->field('expire', 'int', 11)->comment('过期时间'),
            $struct->field('time', 'int', 11)->comment('会话创建时间'),
            $struct->field('ip', 'varchar', 32)->comment('会话创建IP')
        ]);
    }
}
