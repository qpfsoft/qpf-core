<?php
namespace qpf\cache\type;

use qpf;
use qpf\cache\CacheHandler;

class Memcache extends CacheHandler
{
    /**
     * 缓存选项
     * @var array
     */
    protected $options = [
        'host'       => '127.0.0.1',
        'port'       => 11211,
        'expire'     => 0,
        'timeout'    => 0, // 超时时间（单位：毫秒）
        'persistent' => true,
        'prefix'     => '',
        'serialize'  => true,
        'tag_prefix' => 'tag_',
    ];
    
    /**
     * 构造函数
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (!extension_loaded('memcache')) {
            throw new \BadFunctionCallException('not support: memcache');
        }
        
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        
        $this->handler = new \Memcache;
        
        // 支持集群
        $hosts = explode(',', $this->options['host']);
        $ports = explode(',', $this->options['port']);
        
        if (empty($ports[0])) {
            $ports[0] = 11211;
        }
        
        // 建立连接
        foreach ($hosts as $i => $host) {
            $port = $ports[$i] ?? $ports[0];
            $this->options['timeout'] > 0 ?
            $this->handler->addServer($host, (int) $port, $this->options['persistent'], 1, $this->options['timeout']) :
            $this->handler->addServer($host, (int) $port, $this->options['persistent'], 1);
        }
    }

    /**
     * 自增缓存 - 针对数值缓存
     * @param string $name 缓存变量名
     * @param int $step 步长
     * @return false|int
     */
    public function inc($name, $step = 1)
    {
        $this->writeTimes++;
        
        $key = $this->getCacheKey($name);
        
        if ($this->handler->get($key)) {
            return $this->handler->increment($key, $step);
        }
        
        return $this->handler->set($key, $step);
    }

    /**
     * 自减缓存 - 针对数值缓存
     * @param string $name 缓存变量名
     * @param int $step 步长
     * @return false|int
     */
    public function dec($name, $step = 1)
    {
        $this->writeTimes++;

        $key   = $this->getCacheKey($name);
        $value = $this->handler->get($key) - $step;
        $res   = $this->handler->set($key, $value);

        return !$res ? false : $value;
    }

    /**
     * 删除缓存
     * @param string $name 缓存变量名
     * @param bool|false $ttl
     * @return bool
     */
    public function rm($name, $ttl = false)
    {
        $this->writeTimes++;

        $key = $this->getCacheKey($name);

        return false === $ttl ?
        $this->handler->delete($key) :
        $this->handler->delete($key, $ttl);
    }

    /**
     * 读取缓存
     * @param string $name 缓存变量名
     * @param mixed $default 默认值
     * @return mixed
     */
    public function get($name, $default = false)
    {
        $this->readTimes ++;
        
        $result = $this->handler->get($this->getCacheKey($name));
        
        return false !== $result ? $this->unserialize($result) : $default;
    }

    /**
     * 写入缓存
     * @param string $name 缓存变量名
     * @param mixed $value 存储数据
     * @param int|\DateTime $expire 有效时间（秒）
     * @return bool
     */
    public function set($name, $value, $expire = null)
    {
        $this->writeTimes ++;
        
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        
        if (! empty($this->tag) && ! $this->has($name)) {
            $first = true;
        }
        
        $key = $this->getCacheKey($name);
        $expire = $this->getExpireTime($expire);
        $value = $this->serialize($value);
        
        if ($this->handler->set($key, $value, 0, $expire)) {
            isset($first) && $this->setTagItem($key);
            return true;
        }
        
        return false;
    }

    /**
     * 清除缓存
     * @return bool
     */
    public function clear()
    {
        if (!empty($this->tag)) {
            foreach ($this->tag as $tag) {
                $this->clearTag($tag);
            }
            return true;
        }

        $this->writeTimes++;

        return $this->handler->flush();
    }

    /**
     * 判断缓存
     * @param string $name 缓存变量名
     * @return bool
     */
    public function has($name)
    {
        $key = $this->getCacheKey($name);
        
        return false !== $this->handler->get($key);
    }

    /**
     * 清除指定tag的缓存
     * @param string $tag
     */
    public function clearTag($tag)
    {
        // 指定标签清除
        $keys = $this->getTagItems($tag);
        
        foreach ($keys as $key) {
            $this->handler->delete($key);
        }
        
        $tagName = $this->getTagKey($tag);
        $this->rm($tagName);
    }
}