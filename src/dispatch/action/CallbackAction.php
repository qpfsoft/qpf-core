<?php
namespace qpf\dispatch\action;

use qpf;
use qpf\response\Response;

/**
 * 回调动作
 */
class CallbackAction extends ActionBase
{
    /**
     * 执行动作
     */
    public function run(): Response
    {
        $params = array_merge($this->app->request->param(), $this->param);
        
        $data = $this->app->call($this->action, $params);
        
        return $this->app->response->setCode($this->code)->setData($data);
    }
}