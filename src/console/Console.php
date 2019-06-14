<?php
namespace qpf\console;

use qpf;
use qpf\base\Application;

/**
 * 命令行模式
 * 
 * # 语法格式:
 * ```
 * > php qpf <必须指令:可选指令> <参数1|参数2|参数N...>
 * ```
 * # 创建命令:
 * 必须指令: 为执行的脚本名称, 即类
 * 可选指令: 为脚本(类)的操作, 以冒号分割, 未指定默认执行`run`方法.
 * 参数: 传递给指令(类)的方法参数, 按顺序传入.
 * 
 * 
 * Cli模式执行:
 * ```
 * $dir = new \qpf\console\Console();
 * // qpf build:init -v
 * // qpf build:help
 * $result = $dir->execute('qpf build:help init');
 * echor($result);
 * ```
 *
 */
class Console
{
    /**
     * 绑定命令
     * @var array
     */
    public $binds = [];
    /**
     * 当前错误消息
     * @var string
     */
    protected static $errorMessage = '';
    /**
     * 环境变量
     * @var array
     */
    protected static $path = [];
    /**
     * 应用程序
     * @var Application
     */
    protected $app;
    
    /**
     * 构造函数
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
    
    /**
     * CLI模式执行QPF内部命令
     * @param string $command 命令, 格式`qpf build:run`
     * @return mixed
     */
    public function execute($command)
    {
        $_SERVER['argv'] = preg_split('/\s+/', $command);
        $this->bootstrap();
    }
    
    /**
     * 引导程序
     * 
     * ```
     * 命令 <必选参数1:必选参数2> [-option {必选参数1|必选参数2|必选参数3}] [可选参数…] {(默认参数)|参数|参数}
     * ```
     */
    public function bootstrap()
    {
        // 命令标识 `php qpf cms:make `去除 qpf
        array_shift($_SERVER['argv']);
        // 执行命令
        $cmd = array_shift($_SERVER['argv']);
        // 自动定位到run操作
        if(strpos($cmd, ':') === false) {
            $cmd .= ':run';
        }
        // 解析出命令与操作
        list($command, $action) = explode(':', $cmd, 2);
        
        if(empty($command)) {
            return $this->error('setting command');
        }
        
        // 获取命令对应的类名
        $class = $this->getClass($command);

        if(class_exists($class)) {
            
            $object = new $class($this->app);
            
            if(!method_exists($object, $action)) {
                return $this->error('Command not exist `'. $action .'` action!');
            }
            
            try {
                $result = call_user_func_array([$object, $action], $_SERVER['argv']);
            } catch (\Throwable $e) {
                $this->error($e->getMessage() . PHP_EOL . PHP_EOL .
                    "FILE: " . $e->getFile() . PHP_EOL . 
                    'Line: ' . $e->getLine());
            }
            
            if (PHP_SAPI != 'cli' && QPF::$app->isDebug()) {
                echor($result);
            }
            
            // 返回`null`或`true`代表成功
            if($result === null || $result === true) {
                $this->success('Command exec success');
            } elseif($result === false) {
                // 返回`false`, 代表失败
                $this->error('Command exec error');
            } else {
                // 其它情况输出返回值
                $this->log(get_varstr($result));
            }
            
            return $result;
        } else {
            return $this->error('Command does not exist, format `qpf <build:help> opt1 opt2`');
        }
    }
    
    /**
     * 获取命令对应的类
     * @param string $command 命令
     * @return string
     */
    protected function getClass($command)
    {
        if(isset($this->binds[$command])) {
            $class = $this->binds[$command];
        } else {
            $class = $this->getCommandClass($command);
        }
        
        return $class;
    }
    
    /**
     * 获取系统命令类
     * @param string $command 命令
     * @return string
     */
    protected function getCommandClass($command)
    {
        return __NAMESPACE__ . '\\command\\' . strtolower($command) . '\\' . ucfirst($command);
    }
    
    /**
     * 绑定多个命令
     * @param array $commands
     * @return void
     */
    public function binds(array $commands)
    {
        $this->binds = array_merge($this->binds, $commands);
    }
    
    /**
     * 绑定命令
     * @param string $name 命令标识
     * @param \Closure $callback 回调方法
     * @return void
     */
    public function bind($name, \Closure $callback)
    {
        $this->binds[$name] = $callback;
    }
    
    /**
     * 向控制台输出信息
     * @param string $message 信息
     * @return void
     */
    public function log($message)
    {
        if(PHP_SAPI == 'cli') {
            echo PHP_EOL . $message. PHP_EOL;
            exit(1);
        }
    }
    
    /**
     * 输出错误信息
     * @param string $message
     * @return false
     */
    final public function error($message)
    {
        $this->log("\033[;41m $message \x1B[0m\n");
        $this->setError($message);
        
        return false;
    }
        
    /**
     * 设置错误消息
     * @param string $message 信息
     * @return void
     */
    public function setError($message)
    {
        self::$errorMessage = $message;
    }
    
    /**
     * 返回错误信息
     * @return string
     */
    public function getError()
    {
        return self::$errorMessage;
    }
    
    /**
     * 输出成功信息
     * @param string $message
     * @return true
     */
    final public function success($message)
    {
        $this->log("\033[;36m $message \x1B[0m\n");
        
        return true;
    }
}