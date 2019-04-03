<?php
namespace dxkite\apartment\table;

use suda\orm\DataSource;
use suda\orm\TableStruct;
use support\setting\table\AutoCreateTable;

/**
 * 公寓表
 */
class ApartmentTable extends AutoCreateTable
{
    public function __construct()
    {
        parent::__construct('apartment');
    }

    public function onCreateStruct(TableStruct $struct):TableStruct
    {
        return $struct->fields([
            $struct->field('id', 'bigint', 20)->auto()->primary(),
            $struct->field('user', 'bigint', 20)->unique()->null()->default(null)->comment('选择人'),
            $struct->field('build', 'int')->key()->comment('楼宇'),
            $struct->field('floor', 'int')->key()->comment('楼层'),
            $struct->field('room', 'int')->key()->comment('房间号'),
            $struct->field('bed', 'int')->key()->comment('床位号'),
            $struct->field('sex', 'char', 1)->comment('性别'),
            $struct->field('major', 'varchar', 255)->comment('专业'),
            $struct->field('time', 'int', 11)->comment('时间'),
            $struct->field('ip','varchar',255)->comment('选择IP')
        ]);
    }
}
