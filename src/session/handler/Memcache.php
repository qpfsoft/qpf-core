<?php
namespace qpf\session\handler;

use qpf\base\Core;
use qpf\exception\Exception;
use qpf\exception\ConfigException;

/**
 * Memcache Session会话处理程序
 */
class Memcache extends Core implements \SessionHandlerInterface
{
    /**
     * memcache处理程序
     * @var \Memcache
     */
    protected $handler;
    /**
     * memcache主机, 逗号分隔
     * @var string
     */
    protected $host;
    /**
     * memcache端口号, 逗号分隔
     * @var int
     */
    protected $port;
    /**
     * memcache有效期
     * @var int
     */
    protected $expire;
    /**
     * 连接超时时间, 毫秒
     * @var int
     */
    protected $timeout;
    /**
     * 是否长连接
     * @var bool
     */
    protected $persistent;
    /**
     * 前缀名
     * @var string
     */
    protected $name;
    
    /**
     * 初始化session会话
     * @param string $path 存储或检索session的路径
     * @param string $name session名
     * @return bool
     */
    public function open($path, $name)
    {
        // 检查扩展
        if (!extension_loaded('memcache')) {
            throw new Exception('Not loaded memcache Expansion');
        }
        
        $this->handler = new \Memcache();
        
        // 支持群集
        $hosts = explode(',', $this->host);
        $ports = explode(',', $this->port);
        
        if(empty($ports[0])) {
            throw new ConfigException('Session Memcache handler `port` property error');
        }
        
        // 将memcache服务器添加到连接池
        foreach ($hosts as $index => $host) {
            $port = isset($ports[$index]) ? $ports[$index] : $ports[0];
            $this->timeout > 0 ? 
            $this->handler->addserver($host, $port, $this->persistent, 1, $this->timeout) :
            $this->handler->addserver($host, $port, $this->persistent, 1);
        }
        
        return true;
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
        return $this->handler->set($this->name . $id, $data, 0, $this->expire);
    }
    
    /**
     * 删除一个session会话
     * @param string $id session会话ID
     * @return bool
     */
    public function destroy($id)
    {
        return $this->handler->delete($this->name . $id);
    }
    
    /**
     * 清理旧session会话
     * @param int $maxlifetime 最大寿命
     * @return bool
     */
    public function gc($maxlifetime)
    {
        // session清理需基于session.gc_divisor，session.gc_probability和session.gc_maxlifetime设置
        return true;
    }
}