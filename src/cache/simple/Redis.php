<?php
namespace qpf\cache\simple;

use qpf;
use qpf\exception\ConfigException;

/**
 * Redis 缓存
 */
class Redis implements CacheInterface
{
    /**
     * 
     * @var \Redis
     */
    protected $link;
    
    /**
     * 连接
     * @return mixed
     */
    public function connect()
    {
        $config = QPF::$app->config->get('cache.redis');
        if (empty($config)) {
            throw new ConfigException('Redis cache miss config');
        }
        
        $this->link = new \Redis();
        
        if ($this->link->connect($config['host'], $config['port'])) {
           throw new \Exception('Redis cache connect error!'); 
        }
        $this->link->auth($config['password']);
        $this->link->select($config['database']);
    }
    
    /**
     * 设置缓存
     * @param string $name 缓存标识
     * @param mixed $value 缓存值
     * @param int $expire 有效期
     * @return mixed
     */
    public function set($name, $value, $expire)
    {
        if($this->link->set($name, $value)) {
            return $this->link->exists($name, $expire);
        }
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
        return $this->link->del($name);
    }

    /**
     * 刷新缓存
     * @return mixed
     */
    public function flush()
    {
        return $this->link->flushAll();
    }
}