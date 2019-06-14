<?php
use qpf\mail\Mail;

include __DIR__ . '/../../unit.php';

$config = [
    'host'  => 'smtp.163.com',
    'port'  => 25,
    'username'  => 'piliang100@163.com',
    'password'  => 'qpf100',
    'fromname'  => 'piliang200', // 有用
    'frommail'  => 'piliang100@163.com', // 必须与发送帐号一致
];

$mail = new Mail($config);

$result = $mail->send('----@qq.com', '管理员', '标题: 您的已到账', '内容: 你的资料已通过审核!');

echo $result ? '发送成功' : '发送失败';