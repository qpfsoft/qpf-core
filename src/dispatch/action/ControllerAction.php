<?php
namespace qpf\dispatch\action;

use qpf\response\Response;
use qpf\response\ResponseInterface;
use qpf\error\HttpException;

/**
 * 控制器动作
 */
class ControllerAction extends ActionBase
{
    /**
     * 执行控制器
     * @return Response
     */
    public function run(): Response
    {
        if (is_string($this->action)) {
            $route = explode('/', $this->action);
        } else {
            $route = $this->action;
        }
        
        if (count($route) !== 3) {
            throw new HttpException(404, '控制器调度器, 格式 `模块/控制器/操作`, 参数过多或过少!');
        }
        list($app, $controller, $action) = $route;
        
        $this->app->request->setApp($app);
        $this->app->request->setController($controller);
        $this->app->request->setAction($action);
        
        /* @var \qpf\base\Module $module */
        $module = $this->app->web->createModule($route[0]);
        $data = $module->action($controller, $action, $this->param);

        if ($data instanceof ResponseInterface) {
            return $data;
        }
        
        return $this->app->response->setData($data);
    }

    
}