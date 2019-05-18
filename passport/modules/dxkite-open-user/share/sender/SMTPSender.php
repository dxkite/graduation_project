<?php
namespace dxkite\openuser\sender;


use dxkite\openuser\controller\ConfigController;
use suda\application\Application;
use suda\application\exception\ConfigurationException;
use suda\framework\Config;
use support\smtp\Sender;

class SMTPSender extends Sender
{

    /**
     * @var Application
     */
    protected static $application;

    /**
     * SMTPSender constructor.
     * @param Application $application
     * @return SMTPSender
     */
    public static function build(Application $application)
    {
        static::$application = $application;
        static::$application->getDebug()->addIgnorePath(__FILE__);
        $config = ConfigController::loadSMTPConfig($application->getDataPath());
        $server = $config['server'];
        $port = $config['port'] ?? 465;
        $timeout = $config['timeout'] ?? 3;
        $name = $config['name'];
        $password = $config['password'];
        $security = $config['security'] ?? true;
        return new self($server, $port, $timeout, $name, $password, $security);
    }

    protected function log(string $message)
    {
        static::$application->getDebug()->info($message);
    }
}