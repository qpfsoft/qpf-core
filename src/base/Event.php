<?php
namespace qpf\base;

use qpf;

/**
 * 事件处理程序
 * 
 * 观察者模式
 * - 定义了一种一对多的依赖关系
 * - 让多个事件监听者对象同时监听某一个事件, 当事件被触发, 会通知所有监听者
 * 案例:
 * 天气类A, 显示天气界面类B, B就把自己注册到A里, 当A触发天气变化, 就调度B的更新方法.
 * 发布/订阅模式
 * - 
 */
class Event extends Core
{
    
    /**
     * 是否启用事件
     * @var bool
     */
    protected $enable;
    /**
     * 可用事件列表 - 支持别名
     * ```
     * ['事件别名' => '事件名']
     * ```
     * @var array
     */
    protected $events = [
        'AppInit'       => 'AppInit', // 应用初始化
        'AppBegin'      => 'AppBegin', // 应用开启前
        'AppEnd'        => 'AppEnd', // 应用结束后
        'LogLevel'      => 'LogLevel', // 日志级别
        'LogWrite'      => 'LogWrite', // 日志写入
        'ResponseSend'  => 'ResponseSend', // 响应发送
        'ResponseEnd'   => 'ResponseEnd', // 响应结束
    ];
    /**
     * 事件监听者列表
     * ```
     * [
     *      '事件名' => ['监听者1', '监听者2', ...],
     * ]
     * ```
     * @var array
     */
    protected $listener = [];
    /**
     * 事件观察者列表
     * @var array
     */
    protected $observer = [];
    
    public function boot()
    {
        $this->enable(QPF::$app->config->get('app.on_event'));
    }

    /**
     * 是否启用事件响应
     * @param bool $use
     * @return $this
     */
    public function enable($use)
    {
        $this->enable = $use;
        
        return $this;
    }
    
    /**
     * 添加事件监听者
     * @param string $event 事件名称
     * @param mixed $listener 事件监听者
     * @param string $first 是否优先执行, 默认`false`
     */
    public function addEventListener($event, $listener, $first = false)
    {
        if(!$this->enable) {
            return $this;
        }
        
        if (isset($this->events[$event])) {
            $event = $this->events[$event];
        }
        
        if(!isset($this->listener[$event])) {
            $this->listener[$event] = [];
        }
        
        if($first) {
            array_unshift($this->listener[$event], $listener);
        } else {
            $this->listener[$event][] = $listener;
        }
        
        return $this;
    }
    
    /**
     * 添加多个事件监听
     * @param array $event 事件定义
     * @return $this
     */
    public function addEventlisteners(array $events)
    {
        if(!$this->enable) {
            return $this;
        }
        
        foreach ($events as $event => $listeners) {
            if(isset($this->events[$event])) {
                $event = $this->events[$event];
            }
            
            if(!isset($this->listener[$event])) {
                $this->listener[$event] = [];
            }
            
            $this->listener[$event] = array_merge($this->listener, $listeners);
        }
        
        return $this;
    }
    
    /**
     * 事件是否存在监听者
     * @param string $event 事件名
     * @return bool
     */
    public function hasEventListener($event)
    {
        if(isset($this->events[$event])) {
            $event = $this->events[$event];
        }
        
        return isset($this->listener[$event]) && !empty($this->listener[$event]);
    }
    
    /**
     * 移除事件的监听者
     * @param string $event 事件名
     * @param mixed $listener 要移除的监听者
     * @return $this
     */
    public function off($event, $listener = null)
    {
        if(isset($this->events[$event])) {
            $event = $this->events[$event];
        }
        
        if($listener === null) {
            unset($this->listener[$event]);
        } else {
            $remove = false;
            foreach ($this->listener[$event] as $i => $e) {
                if($e[$i] === $listener) {
                    unset($this->listener[$event][$i]);
                    $remove = true;
                }
            }
            
            $this->listener[$event] = array_values($this->listener[$event]);
        }
        
        return $this;
    }
    
    /**
     * 移除所有事件的监听者
     * @return $this
     */
    public function offAll()
    {
        $this->listener = [];
    }
    
    /**
     * 绑定多个事件到可用列表
     * @param array $events 事件列表
     * @return $this
     */
    public function bindEvents(array $events)
    {
        $this->events = array_merge($this->events, $events);
        
        return $this;
    }
    
    public function subscribe($subscriber)
    {
        if(!$this->enable) {
            return $this;
        }
        
        $subscribers = (array) $subscriber;
        
        foreach ($subscribers as $subscriber) {
            if(is_string($subscriber)) {
                $subscriber = QPF::create($subscriber);
            }
            
            if(method_exists($subscriber, 'subscribe')) {
                // 手动订阅
                $subscriber->subscribe($this);
            } else {
                // 自动订阅
                $this->observe($subscriber);
            }
        }
        
        return $this;
    }
    
    /**
     * 自动注册事件监听者
     * @param string|object $observer 观察者
     * @return $this
     */
    public function observe($observer)
    {
        if(!$this->enable) {
            return $this;
        }
        
        if(is_string($observer)) {
            $observer = QPF::create($observer);
        }
        
        $events = array_keys($this->listener);
        
        foreach ($events as $event) {
            $method = 'on' . substr(strrchr($event, '\\'), 1);
            
            if (method_exists($observer, $method)) {
                $this->addEventListener($event, [$observer, $method]);
            }
        }
        
        return $this;
    }
    
    /**
     * 触发事件
     * @param string|object $event 事件名称
     * @param array $params 参数
     * @param bool $once 仅执行一次有效处理
     * @return mixed
     */
    public function trigger($event, array $params = [], $once = false)
    {
        if(!$this->enable) {
            return $this;
        }
        
        if (is_object($event)) {
            $class = get_class($event);
            QPF::container()->instance($class, $event);
            $event = $class;
        }
        
        if(isset($this->events[$event])) {
            $event = $this->events[$event];
        }
        
        if(!isset($this->listener[$event])) {
            $this->listener[$event] = [];
        }
        
        $result = [];
        foreach ($this->listener[$event] as $i => $listener) {
            $result[$i] = $this->execEvent($listener, $params);
            // 仅执行一次有效处理
            if($result[$i] === false || ($result[$i] !== null && $once)) {
                break;
            }
        }
        
        return $once ? end($result) : $result;
    }
    
    /**
     * 执行事件
     * @param mixed $event 事件方法
     * @param array $params 参数
     * @return mixed
     */
    protected function execEvent($event, array $params = [])
    {
        if (!is_string($event)) {
            $call = $event;
        } elseif (strpos($event, '::')) {
            $call = $event;
        } else {
            $call = [$event, 'handle'];
        }
        
        return QPF::container()->call($call, $params);
    }
}