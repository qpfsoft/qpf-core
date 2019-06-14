<?php
namespace qpf\log\storage;

use qpf;

/**
 * 日志存储器接口
 */
abstract class LogStorage
{
    /**
     * 是否启用该存储类型
     * @var string
     */
    public $enable = true;
    /**
     * 要收集的消息级别
     * @var array
     */
    public $level = [];
    /**
     * 已收集的日志信息
     * @var array
     */
    public $log = [];
    /**
     * 保存间隔
     * @var int
     */
    public $interval = 100;
    
    /**
     * 日志存储器进行保存
     * 
     * @return bool 保存成功需返回true来释放内存.
     */
    abstract public function save();
    
    /**
     * 记录需要的日志消息
     * @param array $logs 日志信息集合
     * @param bool $shutdown 是否PHP终止时最后调用
     * @return void
     */
    public function record($logs, $shutdown)
    {
        // 记录所有级别
        if (empty($this->level)) {
           
            if (!QPF::app()->isDebug() && isset($logs['debug'])) {
                unset($logs['debug']);
            }
            
            $this->log = $logs;
        } else {
            // 记录需要的级别
            foreach ($this->level as $level) {
                if(isset($logs[$level])) {
                    $this->log[$level] = $logs[$level];
                }
            }
        }

        // 已收集日志条数
        $count = count($this->log);
        // 在程序终止前, 需要达到保存间隔才写入 $count > 0 && ($shutdown || $this->interval > 0 && $count >= $this->interval)
        if($count > 0 && $shutdown) {
            // 取消保存间隔, 防止保存过程再次触发
            $interval = $this->interval;
            $this->interval = 0;
            $this->save();
            $this->interval = $interval;
            // 保存后释放内存
            $this->log = [];
        }
    }
}