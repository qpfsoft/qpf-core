<?php
namespace qpf\log;

use qpf;
use qpf\base\Core;
use qpf\exceptions\QlassException;
use qpf\log\storage\StorageInterface;
use qpf\exceptions\ConfigException;
use qpf\log\storage\LogStorage;

/**
 * 日志记录
 */
class Log extends Core
{
    /**
     * 系统不可用
     * @var string
     */
    const EMERGENCY = 'emergency';
    /**
     * 警报 - 必须立刻采取行动
     * @var string
     */
    const ALERT = 'alert';
    /**
     * 紧急情况
     * @var string
     */
    const CRITICAL = 'critical';
    const ERROR = 'error';
    const WARNING = 'warning';
    const NOTICE = 'notice';
    const INFO = 'info';
    const DEBUG = 'debug';
    const SQL = 'sql';
    
    /**
     * 消息列表
     *
     * @var array
     */
    protected $log = [];
    /**
     * 日志存储器
     *
     * @var \qpf\log\storage\Base
     */
    protected $storage = [];
    /**
     * 日志授权密钥
     *
     * @var string
     */
    protected $key;
    /**
     * 启用日志保存
     * @var bool
     */
    protected $enableSave;
    
    /**
     * 初始化
     */
    protected function boot()
    {
        // 注册php中止时执行的函数
        register_shutdown_function(function () {
            // 在其他关机函数前调用flush, 可进行日志分发
            $this->flush();
            // 确保有多个关机函数时, 最后调用保存日志
            register_shutdown_function([
                $this,
                'flush'
            ], true);
        });
        // 装载存储器
        $this->parseStorage();
    }
    
    /**
     * 设置日志信息
     * @param string $message 消息
     * @param string $level 消息级别
     * @param array $context 占位符替换值
     * @return $this
     */
    public function setLog($message, $level = 'info', array $context = [])
    {
        if (!$this->enableSave) {
            return;
        }
        
        if (!empty($context) && is_string($message)) {
            $message = $this->interpolate($message, $context);
        }
        
        $this->log[$level][] = $message;
        
        if (PHP_SAPI == 'cli') {
            // TODO 命令行模式, 日志立即写入
            $this->flush(true);
        }
        
        return $this;
    }
    
    /**
     * 返回日志消息
     * @param string $type 信息类型, 默认`null` 即所以类型.
     * @return array
     */
    public function getLog($type = null)
    {
        return $type ? $this->log[$type] : $this->log;
    }

    /**
     * 设置日志授权密钥
     * @param string $key 密钥
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        
        return $this;
    }
    
    /**
     * 返回日志授权密钥
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
    
    /**
     * 检查日志写入权限
     * @param array $allow_key 允许的密钥列表
     * @return bool
     */
    public function check(array $allow_key = [])
    {
        if ($this->key && !empty($allow_key) && !in_array($this->key, $allow_key)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * 将内存中日志信息保存到存储器
     * @param string $shutdown 是否PHP终止时最后调用
     */
    public function flush($shutdown = false)
    {
        $logs = $this->log;
        // 确保日志存储中, 也可继续记录新的信息
        $this->log = [];
        if (empty($this->storage)) {
            throw new ConfigException('Log::$storage is empty!');
        }
        // 分发消息
        $this->save($logs, $shutdown);
    }
    
    /**
     * 关闭当前请求的日志写入
     * @return $this
     */
    public function close()
    {
        $this->enableSave = false;
        $this->log = [];
        
        return $this;
    }
    
    /**
     * 清空日志信息
     * @return $this
     */
    public function clear()
    {
        $this->log = [];
        
        return $this;
    }
    
    /**
     * 保存日志信息
     * @param array $logs 日志集合
     * @param bool $shutdown 是否PHP终止时最后调用
     * @return boolean
     */
    public function save($logs, $shutdown)
    {
        // 未启用保存
        if (!$this->enableSave) {
            return true;
        }
        
        // 检查写入权限
        if(!$this->check()) {
            return false;
        }

        $recordErrors = [];

        /* @var $type \qpf\log\storage\StorageInterface */
        foreach ($this->storage as $type) {
            if (is_object($type)) {
                try {
                    $type->record($logs, $shutdown);
                } catch (\Exception $e) {
                    $type->enable = false;
                    $recordErrors = [
                        'log storage record error!' . QPF::app()->errorHandle()->getHandle()->parse($e),
                        Log::WARNING,
                    ];
                    
                }
            }
        }

        // 日志分发时出现错误, 记录到日志
        if (!empty($recordErrors)) {
            $this->save($recordErrors, true);
        }
    }
    
    /**
     * 写入日志信息
     * @param string $message 日志信息
     * @param string $level 日志级别
     * @param bool $save 是否立即写入存储器
     */
    public function write($message, $level, $save = false)
    {
        $this->log[$level][] = $message;

        if($save) {
            $this->flush($save);
        }
    }
    
    /**
     * 记录日志信息
     * @param string $message 日志信息
     * @param string $level $message
     * @param array $context 占位符替换值
     * @return void
     */
    public function log($message, $level, array $context = [])
    {
        $this->setLog($message, $level, $context);
    }
    
    /**
     * 记录系统不可用消息
     * @param string $message 日志信息
     * @param array $context 占位符替换值
     * @return void
     */
    public function emergency($message, array $context = [])
    {
        $this->setLog($message, __FUNCTION__, $context);
    }
    
    /**
     * 记录警报消息
     * @param string $message 日志信息
     * @param array $context 占位符替换值
     * @return void
     */
    public function alert($message, array $context = [])
    {
        $this->setLog($message, __FUNCTION__, $context);
    }
    
    /**
     * 记录紧急情况
     * @param string $message 日志信息
     * @param array $context 占位符替换值
     * @return void
     */
    public function critical($message, array $context = [])
    {
        $this->setLog($message, __FUNCTION__, $context);
    }
    
    /**
     * 记录错误消息
     * @param string $message 日志信息
     * @param array $context 占位符替换值
     * @return void
     */
    public function error($message, array $context = [])
    {
        $this->setLog($message, __FUNCTION__, $context);
    }
    
    /**
     * 记录警告消息
     * @param string $message 日志信息
     * @param array $context 占位符替换值
     * @return void
     */
    public function warning($message, array $context = [])
    {
        $this->setLog($message, __FUNCTION__, $context);
    }
    
    /**
     * 记录注意消息
     * @param string $message 日志信息
     * @param array $context 占位符替换值
     * @return void
     */
    public function notice($message, array $context = [])
    {
        $this->setLog($message, __FUNCTION__, $context);
    }
    
    /**
     * 记录普通消息
     * @param string $message 日志信息
     * @param array $context 占位符替换值
     * @return void
     */
    public function info($message, array $context = [])
    {
        $this->setLog($message, __FUNCTION__, $context);
    }
    
    /**
     * 记录调试消息
     * @param string $message 日志信息
     * @param array $context 占位符替换值
     * @return void
     */
    public function debug($message, array $context = [])
    {
        $this->setLog($message, __FUNCTION__, $context);
    }
    
    /**
     * 记录sql消息
     * @param string $message 日志信息
     * @param array $context 占位符替换值
     * @return void
     */
    public function sql($message, array $context = [])
    {
        $this->setLog($message, __FUNCTION__, $context);
    }
    
    /**
     * 替换消息中的占位符
     * ```
     * $message = "User {username} created";
     * $context = array('username' => 'bolivar');
     * interpolate($message, $context); // "Username bolivar created"
     * ```
     * @param string $message 消息
     * @param array $context 占位符替换值
     * @return string
     */
    protected function interpolate($message, array $context = [])
    {
        // 构建一个花括号包含的键名的替换数组
        $replace = [];
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }
        
        // 替换记录信息中的占位符，最后返回修改后的记录信息。
        return strtr($message, $replace);
    }
    
    /**
     * 解析日志存储器
     * @throws QlassException
     */
    protected function parseStorage()
    {
        if (empty($this->storage)) {
            throw new QlassException('log storage config is null');
        }
        
        if(is_array($this->storage)) {
            foreach ($this->storage as $type => $config) {
                if(!isset($config['$class'])) {
                    throw new QlassException('log storage ' . $type .' config miss `$class` key name');
                } elseif(isset($config['enable']) && $config['enable']) {
                    // 实例化启用的存储器
                    $this->storage[$type] = QPF::create($config);
                    if (!($this->storage[$type] instanceof LogStorage)) {
                        throw new ConfigException('log storage '. $type .' class not implements StorageInterface');
                    }
                }
            }
        } else {
            throw new ConfigException('log storage not array type');
        }
    }
}