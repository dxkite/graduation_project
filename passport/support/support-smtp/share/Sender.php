<?php


namespace support\smtp;

use Throwable;

/**
 * Class Sender
 * @package support\smtp
 */
class Sender
{
    protected $userName;
    protected $password;
    protected $server;
    protected $port;
    protected $isSecurity;
    protected $timeout;
    protected $socket;
    protected $error;

    /**
     * @var Message
     */
    protected $message;

    /**
     * 创建一个SMTP发送
     *
     * @param string $server SMTP邮件服务器
     * @param integer $port 端口号
     * @param integer $timeout 设置发送超时
     * @param string $name 邮箱用户名
     * @param string $password 邮箱密码
     * @param boolean $isSecurity 是否使用SSL，需要开启 OpenSSL 模块
     */
    public function __construct(string $server, int $port, int $timeout, string $name, string $password, bool $isSecurity = true)
    {
        $this->userName = $name;
        $this->password = $password;
        $this->isSecurity = $isSecurity;
        $this->server = $server;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    /**
     * 发送信息
     *
     * @param Message $message 信息体
     * @return boolean
     * @throws FileNoFoundException
     */
    public function send(Message $message): bool
    {
        $this->message = $message;
        // 覆盖
        if ($this->message->getFrom() == null) {
            $this->message->setFrom($this->userName);
        } else {
            $this->message->setFrom($this->userName, $this->message->getFrom()[1]);
        }
        $commands = $this->getCommand();
        if ($this->isSecurity) {
            if ($this->openSocketSecurity()) {
                foreach ($commands as $command) {
                    $result = $this->sendCommandSecurity($command[0], $command[1]);
                    if ($result) {
                        continue;
                    } else {
                        return false;
                    }
                }
                $this->closeSecutity();
            } else {
                return false;
            }
        } else {
            if ($this->openSocket()) {
                foreach ($commands as $command) {
                    $result = $this->sendCommand($command[0], $command[1]);
                    if ($result) {
                        continue;
                    } else {
                        return false;
                    }
                }
                $this->close();
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * @return array
     * @throws FileNoFoundException
     */
    protected function getCommand()
    {
        $command = [
            ["HELO sendmail\r\n", 250],
        ];
        if (!empty($this->userName)) {
            $command[] = ["AUTH LOGIN\r\n", 334];
            $command[] = [base64_encode($this->userName) . "\r\n", 334];
            $command[] = [base64_encode($this->password) . "\r\n", 235];
        }

        $command[] = ['MAIL FROM: <' . ($this->message->getFrom()[0] ?? $this->userName) . ">\r\n", 250];
        if (!empty($emails = $this->message->getTo())) {
            foreach ($emails as $email) {
                $command[] = ["RCPT TO: <" . $email[0] . ">\r\n", 250];
            }
        }
        if (!empty($emails = $this->message->getCc())) {
            foreach ($emails as $email) {
                $command[] = ["RCPT TO: <" . $email[0] . ">\r\n", 250];
            }
        }
        if (!empty($emails = $this->message->getBcc())) {
            foreach ($emails as $email) {
                $command[] = ["RCPT TO: <" . $email[0] . ">\r\n", 250];
            }
        }
        $command[] = ["DATA\r\n", 354];
        $command[] = [$this->getData(), 250];
        $command[] = ["QUIT\r\n", 221];
        return $command;
    }

    /**
     * @return string
     * @throws FileNoFoundException
     */
    protected function getData()
    {
        $data = $this->message->getHeader();
        $data .= 'Subject: =?UTF-8?B?' . base64_encode($this->message->getSubject()) . '?=' . "\r\n";
        $data .= $this->message->getMessage();
        return $data;
    }

    protected function openSocket()
    {
        //创建socket资源
        $this->socket = null;
        set_error_handler(null);
        $this->socket = fsockopen($this->server, $this->port, $errno, $errstr, $this->timeout);
        restore_error_handler();
        if (!$this->socket) {
            $this->setError($errstr);
            return false;
        }
        $str = fread($this->socket, 1024);
        if (!preg_match("/220+?/", $str)) {
            $this->setError($str);
            return false;
        }
        return true;
    }

    protected function openSocketSecurity()
    {
        $remoteAddr = 'tcp://' . $this->server . ':' . $this->port;
        $this->socket = null;
        set_error_handler(null);
        $this->socket = stream_socket_client($remoteAddr, $errno, $errstr, $this->timeout);
        restore_error_handler();
        if (!$this->socket) {
            $this->setError($errstr);
            return false;
        }
        stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_SSLv23_CLIENT);
        stream_set_blocking($this->socket, 1); //设置阻塞模式
        $str = fread($this->socket, 1024);
        if (!preg_match("/220+?/", $str)) {
            $this->setError($str);
            return false;
        }
        return true;
    }

    protected function close()
    {
        if (!is_null($this->socket) && is_object($this->socket)) {
            fclose($this->socket);
            return true;
        }
        return false;
    }

    protected function closeSecutity()
    {
        if (!is_null($this->socket) && is_object($this->socket)) {
            stream_socket_shutdown($this->socket, STREAM_SHUT_WR);
            return true;
        }
        return false;
    }

    protected function sendCommand(string $command, int $stateReturn = null)
    {
        $this->log('send ' . trim($command));
        try {
            if (fwrite($this->socket, $command, strlen($command))) {
                if (is_null($stateReturn)) {
                    return true;
                }
                $data = trim(fread($this->socket, 1024));
                if ($data) {
                    if (preg_match('/^' . $stateReturn . '+?/', $data)) {
                        return true;
                    } else {
                        $this->setError($data);
                        return false;
                    }
                } else {
                    $this->setError($command . ' read failed');
                    return false;
                }
            } else {
                $this->setError($command . ' send failed');
                return false;
            }
        } catch (Throwable $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    protected function sendCommandSecurity(string $command, int $stateReturn = null)
    {
        $this->log('send ' . trim($command));
        try {
            if (fwrite($this->socket, $command)) {
                if (is_null($stateReturn)) {
                    return true;
                }
                $data = trim(fread($this->socket, 1024));
                if ($data) {
                    if (preg_match('/^' . $stateReturn . '+?/', $data)) {
                        return true;
                    } else {
                        $this->setError($data);
                        return false;
                    }
                } else {
                    $this->setError($command . ' read failed');
                    return false;
                }
            } else {
                $this->setError($command . ' send failed');
                return false;
            }
        } catch (Throwable $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    public function getError()
    {
        return $this->error;
    }

    protected function setError(string $error)
    {
        $this->error = $error;
        $this->log($error);
    }

    protected function log(string $message)
    {
    }
}