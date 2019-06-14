<?php
namespace qpf\mail;

use qpf;
use qpf\base\Core;

/**
 * 发送邮件
 */
class Mail extends Core
{
    /**
     * 服务器是否使用SSL
     * @var string
     */
    public $ssl = false;
    /**
     * 邮箱SMTP服务器
     * @var string
     */
    public $host;
    /**
     * SMTP服务器端口
     * @var int
     */
    public $port;
    /**
     * SMTP服务器的用户帐号
     * @var string
     */
    public $username;
    /**
     * SMTP服务器的授权密码
     * @var string
     */
    public $password;
    /**
     * 发件人名称
     * 收件人在收取邮件时显示的发件人名称
     * @var string
     */
    public $fromname;
    /**
     * 发件人邮箱地址
     * 收件人在收取邮件时显示的邮箱地址
     * 与SMTP的帐号设置一样即可
     * @var string
     */
    public $frommail;
    
    /**
     * 发送邮件
     * @param string $usermail 收件人邮箱
     * @param string $username 收件人名称, 一般无效果
     * @param string $title 邮件标题
     * @param string|callable $body 邮件内容
     * @return bool 是否发送成功
     */
    public function send($usermail, $username, $title, $body)
    {
        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->CharSet = 'utf-8';
        // 邮箱服务器
        $mail->Host = $this->host;
        $mail->Port = $this->port;
        $mail->SMTPAuth = true;
        // 登录用户
        $mail->Username = $this->username;
        $mail->Password = $this->password;
        if ($this->ssl) {
            $mail->SMTPSecure = 'tls';
        }
        $mail->setFrom($this->frommail, $this->fromname);
        $mail->addAddress($usermail, $username);
        
        $mail->Subject = $title;
        
        if ($body instanceof \Closure) {
            $body = $body();
        }
        $mail->msgHTML($body);
        
        return $mail->send();
    }
}