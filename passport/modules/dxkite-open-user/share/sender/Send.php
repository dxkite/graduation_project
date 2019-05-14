<?php


namespace dxkite\openuser\sender;


use Exception;
use Qcloud\Sms\SmsSingleSender;
use suda\application\Application;
use suda\application\exception\ConfigurationException;
use suda\framework\Config;
use support\smtp\FileNoFoundException;
use support\smtp\Message;


class Send
{
    /**
     * 发送邮件
     * @param Application $application
     * @param string $name
     * @param string $mail
     * @param string $code
     * @param string $expire
     * @return bool
     */
    public static function mail(Application $application, string $name, string $mail, string $code, string $expire)
    {
        $sender = SMTPSender::build($application);
        $message = new Message('邮箱验证', sprintf("%s：你的邮箱验证码为：%s, 本次验证在 %s 分钟内有效", $name, $code, $expire));
        $message->setTo($mail, $name);
        try {
            return $sender->send($message);
        } catch (FileNoFoundException $e) {
            $application->debug()->error($e->getMessage());
            return false;
        }
    }

    /**
     * 发送短信
     * @param Application $application
     * @param string $name
     * @param string $phone
     * @param string $code
     * @param string $expire
     * @return bool|string
     */
    public static function sortMessage(Application $application, string $name, string $phone, string $code, string $expire)
    {
        $config = self::getConfig($application);
        $params = [$name, $code, $expire];
        $appId = $config['app-id'];
        $appKey = $config['app-key'];
        $singleSender = new SmsSingleSender($appId, $appKey);
        $sign = $config['sign'];
        $templateId = $config['template'];
        try {
            return $singleSender->sendWithParam("86", $phone, $templateId, $params, $sign, "", "");
        } catch (Exception $e) {
            $application->debug()->error($e->getMessage());
            return false;
        }
    }

    protected static function getConfig(Application $application)
    {
        $data = new \suda\application\Resource($application->getDataPath());
        $path = $data->getConfigResourcePath('config/tencent-sms');
        if ($path !== null) {
            $config = Config::loadConfig($path);
            if (is_array($config)) {
                return $config;
            }
        }
        throw new ConfigurationException('config @data/config/tencent-sms not found');
    }
}