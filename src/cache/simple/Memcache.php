<?php
namespace qpf\cache\simple;

use qpf;
use qpf\exception\ConfigException;

/**
 * Memcache 缓存
 */
class Memcache implements CacheInterface
{
    /**
     * 缓存连接
     * @var \Memcache
     */
    protected $link;
    
    /**
     * 连接
     * @return mixed
     */
    public function connect()
    {
        $config = QPF::$app->config->get('cache.memcache');
        
        if (!extension_loaded('memcache')) {
            throw new \BadFunctionCallException('not support: memcache');
        }
        
        $this->link = new \Memcache();
        
        if(!isset($config['host']) && !isset($config['port'])) {
            throw new ConfigException('Memcache miss `host, port` config itme');
        }
        $this->link->addserver($config['host'], $config['port']);
    }
    
    /**
     * 设置缓存
     * @param string $name 缓存标识
     * @param mixed $value 缓存值
     * @param int $expire 有效期秒, `0`代表永不过期, 最大2592000秒
     * @return mixed
     */
    public function set($name, $value, $expire = 0)
    {
        return $this->link->set($name, $value, 0, $expire);
    }
    
    /**
     * 获取缓存
     * @param string $name 缓存标识
     * @return mixed
     */
    public function get($name)
    {
        return $this->link->get($name);
    }
    
    /**
     * 删除缓存
     * @param string $name 缓存标识
     * @return mixed
     */
    public function delete($name)
    {
        return $this->link->delete($name);
    }
    
    /**
     * 刷新缓存
     * @return mixed
     */
    public function flush()
    {
        return $this->link->flush();
    }
}