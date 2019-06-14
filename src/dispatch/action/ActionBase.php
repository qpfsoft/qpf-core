<?php
namespace qpf\dispatch\action;

use qpf;
use qpf\base\Application;
use qpf\response\Response;

/**
 * 调度动作基类
 */
abstract class ActionBase
{
    /**
     * 应用程序
     * @var Application
     */
    protected $app;
    /**
     * 调度操作
     * @var mixed
     */
    protected $action;
    /**
     * 调度参数
     * @var array
     */
    protected $param;
    /**
     * 状态码
     * @var int
     */
    protected $code = 200;
    
    /**
     * 构造函数
     * @param mixed $action 调度信息
     * @param array $param 调度参数
     * @param int $code 状态码
     */
    public function __construct($action, array $param = [], $code = null)
    {
        $this->app = QPF::app();
        $this->action = $action;
        $this->param = $param;
        $this->code = $code;
    }
    
    /**
     * 抽象方法, 实现调度执行
     * @return Response
     */
    abstract public function run(): Response;
    
    /**
     * 返回操作信息
     * @return $this
     */
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * 返回操作参数
     * @return array
     */
    public function getParam(): array
    {
        return $this->param;
    }
    
    /**
     * 返回状态码
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }
}