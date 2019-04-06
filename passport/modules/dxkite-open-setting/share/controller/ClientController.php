<?php
namespace dxkite\openuser\setting\controller;

use suda\orm\TableStruct;
use support\setting\PageData;
use suda\orm\exception\SQLException;
use dxkite\openuser\setting\table\ClientTable;


class UserController
{
    /**
     * 用户表
     *
     * @var ClientTable
     */
    protected $table;

    
    public function __construct()
    {
        $this->table = new ClientTable;
    }
    
    public function add(string $name, string $discription) {
        
    }
}
