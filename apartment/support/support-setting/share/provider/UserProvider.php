<?php
namespace support\setting\provider;

use support\setting\exception\UserException;

class UserProvider
{
 
    /**
     * 登陆 
     *
     * @param string $account 账号
     * @param string $password 密码
     * @return null|array 能登陆则非空
     */
    public function signin(string $account, string $password): ?array
    {
        
    }
}
