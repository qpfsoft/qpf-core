<?php
namespace qpf\dispatch\action;

use \qpf\response\Response;

/**
 * URL动作
 * 
 * - 跳转第三方URL地址
 */
class UrlAction extends ActionBase
{
    
    /**
     * 执行
     */
    public function run(): Response
    {
        header('Location: ' . $this->action);
        exit;
    }
}