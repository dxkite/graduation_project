<?php
namespace dxkite\apartment\table;

use suda\database\DataSource;
use suda\database\struct\TableStruct;
use suda\application\database\Table;
use support\setting\table\AutoCreateTable;
use suda\database\connection\creator\MySQLTableCreator;

/**
 * 学生表
 */
class StudentTable extends AutoCreateTable
{
    public function __construct()
    {
        parent::__construct('student');
    }

    public function onCreateStruct(TableStruct $struct):TableStruct
    {
        return $struct->fields([
            $struct->field('id', 'bigint', 20)->auto()->primary(),
            $struct->field('user', 'bigint', 20)->key()->comment('用户ID'),
            $struct->field('number', 'int', 8)->key()->comment('学号'),
            $struct->field('name', 'varchar', 255)->comment('姓名'),
            $struct->field('exam_number', 'varchar', 255)->comment('考生号'),
            $struct->field('idcard', 'varchar', 18)->key()->comment('身份证号'),
            $struct->field('sex', 'char', 1)->key()->comment('性别'),
            $struct->field('major', 'varchar', 255)->comment('录取专业'),
            $struct->field('class', 'varchar', 255)->comment('录取班级'),
            $struct->field('arrearage', 'int', 11)->key()->default(0)->comment('欠费金额'),
            $struct->field('selected', 'tinyint', 1)->key()->default(0)->comment('是否已经选择'),
            $struct->field('export', 'tinyint', 1)->key()->default(0)->comment('导出标记')
        ]);
    }
}
