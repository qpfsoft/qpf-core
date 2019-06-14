<?php
namespace qpf\session\handler;

use qpf\base\Core;
use qpf\exception\Exception;

/**
 * Redis Session会话处理程序
 */
class Redis extends Core implements \SessionHandlerInterface
{
    /**
     * Redis处理程序
     * @var \Redis
     */
    protected $handler;
    /**
     * redis主机
     * @var string
     */
    protected $host;
    /**
     * redis端口号
     * @var int
     */
    protected $port;
    /**
     * session有效期, 秒
     * @var int
     */
    protected $expire;
    /**
     * 连接超时时间, 秒
     * @var int
     */
    protected $timeout;
    /**
     * 是否长连接
     * @var bool
     */
    protected $persistent;
    /**
     * 连接密码
     * @var string
     */
    protected $password;
    /**
     * 操作库
     * @var int
     */
    protected $select;
    /**
     * 前缀名
     * @var string
     */
    protected $name;
    /**
     * 模拟锁的前缀名
     * @var string
     */
    protected $lockPrefix;
    
    public function init()
    {
        if(empty($this->lockPrefix)) {
            $this->lockPrefix = 'LOCK_PREFIX_';
        }
    }
    
    /**
     * 初始化session会话
     * @param string $path 存储或检索session的路径
     * @param string $name session名
     * @return bool
     */
    public function open($path, $name)
    {
        // 检查扩展
        if (extension_loaded('redis')) {
            $this->handler = new \Redis();
            $this->connectRedis($this->host, $this->port, $this->timeout, $this->persistent);
            $this->auth($this->password);
            $this->select($this->select);
        } else {
            throw new Exception('Not loaded redis Expansion');
        }
        
        return true;
    }
    
    /**
     * 连接redis服务
     * @param string $host 主机地址
     * @param string $prot 端口号
     * @param int $timeout 连接超时时间, 秒
     * @param bool $persistent 是否长连接
     * @return void
     */
    protected function connectRedis($host, $prot, $timeout, $persistent)
    {
        $method = $persistent ? 'pconnect' : 'connect';
        $this->handler->$method($host, $prot, $timeout);
    }
 
    protected function auth($pwd)
    {
        if (!empty($pwd)) {
            $this->handler->auth($pwd);
        }
    }
    
    protected function select($select)
    {
        if ($select != 0) {
            $this->handler->select($select);
        }
    }
    
    /**
     * 关闭session会话
     * @return bool
     */
    public function close()
    {
        $this->gc(ini_get('session.gc_maxlifetime'));
        $this->handler->close();
        $this->handler = null;
        
        return true;
    }
    
    /**
     * 读取session数据
     * @param string $id session会话ID
     * @return string
     */
    public function read($id)
    {
        return (string) $this->handler->get($this->name . $id);
    }
    
    /**
     * 写入session数据
     * @param string $id session会话ID
     * @param string $data 数据
     * @return bool
     */
    public function write($id, $data)
    {
        if ($this->expire > 0) {
            $result = $this->handler->setex($this->name . $id, $this->expire, $data);
        } else {
            $result = $this->handler->set($this->name . $id, $data);
        }
        return (bool) $result;
    }
    
    /**
     * 删除一个session会话
     * @param string $id session会话ID
     * @return bool
     */
    public function destroy($id)
    {
        return $this->handler->delete($this->name . $id) > 0;
    }
    
    /**
     * 清理旧session会话
     * @param int $maxlifetime 最大寿命
     * @return bool
     */
    public function gc($maxlifetime)
    {
        return true;
    }
    
    /**
     * 实现加锁机制
     * @param string $id 用于加锁的会话ID
     * @param int $timeout 默认过期时间, 默认`10`秒
     * @return bool
     */
    public function lock($id, $timeout = 10)
    {
        if ($this->handler === null) {
            $this->open('', '');
        }
        
        $lockKey = $this->lockPrefix . $id;
        // 加锁
        $isLock = $this->handler->setex($lockKey, 1);
        if ($isLock) {
            // 设置锁过期时间, 防止锁死
            $this->handler->expire($lockKey, $timeout);
            return true;
        }
        
        return false;
    }
    
    /**
     * 实现解锁机制
     * @param string $id 用于加锁的会话ID
     * @return void
     */
    public function unlock($id)
    {
        if ($this->handler === null) {
            $this->open('', '');
        }
        
        $this->handler->del($this->lockPrefix . $id);
    }
}