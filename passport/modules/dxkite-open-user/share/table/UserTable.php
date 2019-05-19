<?php
namespace dxkite\openuser\table;

use suda\orm\struct\TableStruct;
use support\setting\table\AutoCreateTable;

/**
 * 管理员表
 */
class UserTable extends AutoCreateTable
{
    const FREEZE = 0;   // 禁用登陆
    const NORMAL = 1;  //  正常状态
    const CREATED = 1;  // 刚刚创建

    const CODE_CHECK_EMAIL = 1; // 验证邮箱
    const CODE_CHECK_MOBILE = 2; // 验证手机号

    const CODE_RESET_PASSWORD_BY_EMAIL = 3; // 重置密码
    const CODE_RESET_PASSWORD_BY_MOBILE = 4; // 重置密码

    public function __construct()
    {
        parent::__construct('open_user');
    }

    public function onCreateStruct(TableStruct $struct):TableStruct
    {
        return $struct->fields([
            $struct->field('id', 'bigint', 20)->auto()->primary(),
            $struct->field('name', 'varchar', 128)->unique()->comment('用户名'),
            $struct->field('email', 'varchar', 128)->unique()->default(null)->comment('邮箱'),
            $struct->field('mobile', 'varchar', 128)->unique()->default(null)->comment('手机号'),
            $struct->field('password', 'varchar', 255)->comment('密码'),
            $struct->field('headimg', 'varchar', 512)->comment('头像'),
            $struct->field('mobile_checked', 'tinyint', 0)->default(0)->comment('短信验证'),
            $struct->field('mobile_send', 'int', 11)->default(0)->comment('上次发送短信时间'),
            $struct->field('email_checked', 'tinyint', 0)->default(0)->comment('邮箱验证'),
            $struct->field('code', 'varchar', 128)->default(null)->comment('验证码'),
            $struct->field('code_type', 'int', 10)->default(0)->comment('验证类型'),
            $struct->field('code_expires', 'int', 11)->default(null)->comment('验证时间'),
            $struct->field('signup_ip', 'varchar', 32)->comment('注册IP'),
            $struct->field('signup_time', 'int', 11)->key()->comment('注册时间'),
            $struct->field('status', 'tinyint', 1)->key()->default(UserTable::NORMAL)->comment('用户状态'),
        ]);
    }
}
