<?php
namespace qpf\dispatch\action;

use \qpf\response\Response;

/**
 * 响应动作
 */
class ResponseAction extends ActionBase
{
    public function run()
    {
        // 直接响应内容
        return $this->app->response->setData($this->action);
    }
}