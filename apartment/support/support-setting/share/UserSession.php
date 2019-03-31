<?php
namespace support\setting;

use support\setting\table\SessionTable;

class UserSession
{
    /**
     * 会话ID
     *
     * @var string
     */
    protected $id;

    /**
     * 会话组
     *
     * @var string
     */
    protected $group;

    /**
     * 会话Token
     *
     * @var string
     */
    protected $token;

    /**
     * 用户ID
     *
     * @var string
     */
    protected $userId;

    /**
     * 过期时间
     *
     * @var int
     */
    protected $expireTime;

    /**
     * 心跳时间
     *
     * @var integer
     */
    protected static $beat = 60;

    /**
     * 创建会话
     *
     * @param string $userId 用户ID
     * @param integer $expireIn 过期时间
     * @param string $group 会话组
     * @return UserSession
     */
    public static function create(string $userId, int $expireIn, string $group = '0'): UserSession
    {
        $table = new SessionTable;
        $session = new static;
        $session->group = $group;
        // 用户会话有效
        if ($data = $table->run($table->read('id', 'expire', 'token')->where([
            'group' => $group,
            'grantee' => $userId,
            'expire' => ['>', time()],
        ])->one())) {
            $session->id = $data['id'];
            $session->token = $data['token'];
            $session->expireTime = $data['expire'];
            $session->userId = $data['grantee'];
            // 小于10倍心跳时长则更新
            $limit = time() + static::$beat * 10;
            if ($data['expire'] < $limit) {
                $session->expireTime = $session->expireTime + $beat;
                $table->run($table->write('expire', $session->expireTime)->where(['id' => $data['id']]));
            }
        } else {
            // 创建新的会话
            $session->expireTime = time() + $expireIn;
            $session->userId = $userId;
            $session->token = str_replace('=', '', base64_encode(\md5(\microtime(true).$userId.$group.$expireIn, true)));
            $session->id = $table->run($table->write([
                'group' => $group,
                'grantee' => $userId,
                'expire' => $session->expireTime,
                'token' => $session->token,
            ])->id());
        }
        return $session;
    }

    /**
     * 从Token中登陆
     *
     * @param string $token
     * @return UserSession
     */
    public static function load(string $token, string $group = '0'):UserSession
    {
        $table = new SessionTable;
        $session = new static;
        $session->group = $group;
        // 用户会话有效
        if ($data = $table->run($table->read('id', 'expire', 'token')->where([
            'group' => $group,
            'token' => $token,
            'expire' => ['>', time()],
        ])->one())) {
            $session->id = $data['id'];
            $session->token = $data['token'];
            $session->expireTime = $data['expire'];
            $session->userId = $data['grantee'];
            // 小于10倍心跳时长则更新
            $limit = time() + static::$beat * 10;
            if ($data['expire'] < $limit) {
                $session->expireTime = $session->expireTime + $beat;
                $table->run($table->write('expire', $session->expireTime)->where(['id' => $data['id']]));
            }
        } else {
            // 会话无效
            $session->expireTime = time() ;
            $session->userId = '';
            $session->token = '';
        }
        return $session;
    }
 
    /**
     * 模拟用户
     *
     * @param string $userId
     * @param integer $exporeIn
     * @param string $group
     * @return UserSession
     */
    public static function simulate(string $userId, int $exporeIn, string $group = '0'):UserSession
    {
        $session = new static;
        $session->group = $group;
        $session->expireTime = time() + $exporeIn;
        $session->userId = $userId;
        $session->token = str_replace('=', '', base64_encode(\md5(\microtime(true).$userId.$group.$expireIn, true)));
        return $session;
    }

    /**
     * Get 会话ID
     *
     * @return  string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set 会话ID
     *
     * @param  string  $id  会话ID
     *
     * @return  self
     */
    public function setId(string $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get 会话组
     *
     * @return  string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set 会话组
     *
     * @param  string  $group  会话组
     *
     * @return  self
     */
    public function setGroup(string $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get 会话Token
     *
     * @return  string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set 会话Token
     *
     * @param  string  $token  会话Token
     *
     * @return  self
     */
    public function setToken(string $token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get 用户ID
     *
     * @return  string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set 用户ID
     *
     * @param  string  $userId  用户ID
     *
     * @return  self
     */
    public function setUserId(string $userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get 过期时间
     *
     * @return  int
     */
    public function getExpireTime()
    {
        return $this->expireTime;
    }

    /**
     * Set 过期时间
     *
     * @param  int  $expireTime  过期时间
     *
     * @return  self
     */
    public function setExpireTime(int $expireTime)
    {
        $this->expireTime = $expireTime;

        return $this;
    }

    /**
     * Get 心跳时间
     *
     * @return  integer
     */
    public static function getBeat()
    {
        return static::$beat;
    }

    /**
     * Set 心跳时间
     *
     * @param  integer  $beat  心跳时间
     *
     * @return  self
     */
    public static function setBeat($beat)
    {
        static::$beat = $beat;
    }
}
