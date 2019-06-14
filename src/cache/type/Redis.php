<?php
namespace qpf\cache\type;

use qpf\cache\CacheHandler;

/**
 * Redis缓存
 * 
 * 适合单机部署、有前端代理实现高可用的场景
 * 性能最好有需要在业务层实现读写分离、或者使用RedisCluster的需求，请使用Redisd驱动
 * 
 * 要求安装phpredis扩展：https://github.com/nicolasff/phpredis
 */
class Redis extends CacheHandler
{
    protected $options = [
        'host'       => '127.0.0.1',
        'port'       => 6379,
        'password'   => '',
        'select'     => 0,
        'timeout'    => 0,
        'expire'     => 0,
        'persistent' => false,
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
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        
        if (extension_loaded('redis')) {
            $this->handler = new \Redis;
            
            if ($this->options['persistent']) {
                $this->handler->pconnect($this->options['host'], $this->options['port'], $this->options['timeout'], 'persistent_id_' . $this->options['select']);
            } else {
                $this->handler->connect($this->options['host'], $this->options['port'], $this->options['timeout']);
            }
            
            if ('' != $this->options['password']) {
                $this->handler->auth($this->options['password']);
            }
            
            if (0 != $this->options['select']) {
                $this->handler->select($this->options['select']);
            }
        } elseif (class_exists('\Predis\Client')) {
            $params = [];
            foreach ($this->options as $key => $val) {
                if (in_array($key, ['aggregate', 'cluster', 'connections', 'exceptions', 'prefix', 'profile', 'replication', 'parameters'])) {
                    $params[$key] = $val;
                    unset($this->options[$key]);
                }
            }
            
            if ('' == $this->options['password']) {
                unset($this->options['password']);
            }
            
            $this->handler = new \Predis\Client($this->options, $params);
            
            $this->options['prefix'] = '';
        } else {
            throw new \BadFunctionCallException('not support: redis');
        }
    }

    /**
     * 自增缓存 - 针对数值缓存
     * @access public
     * @param string $name 缓存变量名
     * @param int $step 步长
     * @return false|int
     */
    public function inc($name, $step = 1)
    {
        $this->writeTimes++;

        $key = $this->getCacheKey($name);

        return $this->handler->incrby($key, $step);
    }

    /**
     * 自减缓存 - 针对数值缓存
     * @access public
     * @param string $name 缓存变量名
     * @param int $step 步长
     * @return false|int
     */
    public function dec($name, $step = 1)
    {
        $this->writeTimes++;

        $key = $this->getCacheKey($name);

        return $this->handler->decrby($key, $step);
    }

    /**
     * 删除缓存
     * @param string $name 缓存变量名
     * @return bool
     */
    public function rm(string $name)
    {
        $this->writeTimes++;

        $this->handler->del($this->getCacheKey($name));
        return true;
    }

    /**
     * 读取缓存
     * @param string $name 缓存变量名
     * @param mixed $default 默认值
     * @return mixed
     */
    public function get($name, $default = false)
    {
        $this->readTimes++;

        $value = $this->handler->get($this->getCacheKey($name));

        if (is_null($value) || false === $value) {
            return $default;
        }

        return $this->unserialize($value);
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value 存储数据
     * @param integer|\DateTime $expire 有效时间（秒）
     * @return bool
     */
    public function set($name, $value, $expire = null)
    {
        $this->writeTimes++;

        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }

        if (!empty($this->tag) && !$this->has($name)) {
            $first = true;
        }

        $key    = $this->getCacheKey($name);
        $expire = $this->getExpireTime($expire);

        $value = $this->serialize($value);

        if ($expire) {
            $this->handler->setex($key, $expire, $value);
        } else {
            $this->handler->set($key, $value);
        }

        isset($first) && $this->setTagItem($key);

        return true;
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

        $this->handler->flushDB();
        return true;
    }

    /**
     * 判断缓存
     * @param string $name 缓存变量名
     * @return bool
     */
    public function has($name)
    {
        return $this->handler->exists($this->getCacheKey($name));
    }
    
    /**
     * 清除指定tag的缓存
     * @param string $tag
     */
    public function clearTag($tag)
    {
        // 指定标签清除
        $keys = $this->getTagItems($tag);
        
        $this->handler->del($keys);
        
        $tagName = $this->getTagKey($tag);
        $this->handler->del($tagName);
    }

    /**
     * 更新标签
     * @access protected
     * @param string $name 缓存标识
     * @return void
     */
    protected function setTagItem($name)
    {
        if (!empty($this->tag)) {
            foreach ($this->tag as $tag) {
                $tagName = $this->getTagKey($tag);
                $this->handler->sAdd($tagName, $name);
            }
            
            $this->tag = null;
        }
    }

    /**
     * 获取标签包含的缓存标识
     * @param string $tag 缓存标签
     * @return array
     */
    public function getTagItems(string $tag)
    {
        $tagName = $this->getTagKey($tag);
        return $this->handler->sMembers($tagName);
    }
}