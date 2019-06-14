<?php
namespace qpf\session\handler;

use qpf\base\Core;
use qpf\exception\Exception;
use qpf\exception\ConfigException;

/**
 * Memcached Session会话处理程序
 */
class Memcached extends Core implements \SessionHandlerInterface
{
    /**
     * memcached处理程序
     * @var \Memcached
     */
    protected $handler;
    /**
     * memcached主机, 逗号分隔
     * @var string
     */
    protected $host;
    /**
     * memcached端口号, 逗号分隔
     * @var int
     */
    protected $port;
    /**
     * memcached有效期
     * @var int
     */
    protected $expire;
    /**
     * 连接超时时间, 毫秒
     * @var int
     */
    protected $timeout;
    /**
     * 前缀名
     * @var string
     */
    protected $name;
    /**
     * 连接帐号
     * @var string
     */
    protected $username;
    /**
     * 连接密码
     * @var string
     */
    protected $password;
    
    /**
     * 初始化session会话
     * @param string $path 存储或检索session的路径
     * @param string $name session名
     * @return bool
     */
    public function open($path, $name)
    {
        // 检查扩展
        if (!extension_loaded('memcached')) {
            throw new Exception('Not loaded memcached Expansion');
        }
        
        $this->handler = new \Memcached();
        
        // 设置连接超时
        if ($this->timeout > 0) {
            $this->handler->setOption(\Memcached::OPT_CONNECT_TIMEOUT, $this->config['timeout']);
        }
        
        // 支持群集
        $hosts = explode(',', $this->host);
        $ports = explode(',', $this->port);
        
        if(empty($ports[0])) {
            throw new ConfigException('Session Memcached handler `port` property error');
        }
        
        
        $servers = [];
        
        foreach ($hosts as $index => $host) {
            $port = isset($ports[$index]) ? $ports[$index] : $ports[0];
            $servers[] = [$host, $port, 1];
        }
        // 添加memcached服务器连接池
        $this->handler->addServers($servers);
        
        if (!empty($this->username)) {
            $this->handler->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);
            $this->handler->setSaslAuthData($this->username, $this->password);
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
        $this->handler->quit();
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
        return $this->handler->set($this->name . $id, $data, $this->expire);
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