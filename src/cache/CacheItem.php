<?php
namespace qpf\cache;

use qpf\psr\cache\CacheItemInterface;

/**
 * CacheItem 缓存项
 *
 * 该类代表一个在缓存池中缓存项的操作对象
 */
class CacheItem implements CacheItemInterface
{

    /**
     * 缓存Key
     * @var string
     */
    protected $key;

    /**
     * 缓存内容
     * @var mixed
     */
    protected $value;

    /**
     * 过期时间
     * @var int|DateTimeInterface
     */
    protected $expire;

    /**
     * 缓存tag
     * @var string
     */
    protected $tag;

    /**
     * 缓存是否命中
     * @var bool
     */
    protected $isHit = false;

    /**
     * 构造函数
     * @param string $key
     */
    public function __construct($key = null)
    {
        $this->key = $key;
    }

    /**
     * 设置缓存项的键名
     * @param string $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * 返回缓存项的键名
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * 返回当前缓存项的有效期
     * @return null|int|\DateTimeInterface
     */
    public function getExpire()
    {
        if ($this->expire instanceof \DateTimeInterface) {
            return $this->expire;
        }
        
        return $this->expire ? $this->expire - time() : null;
    }

    /**
     * 返回缓存项Tag
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * 获取当前缓存项的值
     * @return mixed
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * 确认缓存项的检查是否命中
     * @return bool
     */
    public function isHit()
    {
        return $this->isHit;
    }

    /**
     * 设置当前缓存项的值
     * @param mixed $value
     * @return $this
     */
    public function set($value)
    {
        $this->value = $value;
        $this->isHit = true;
        return $this;
    }

    /**
     * 设置当前缓存项的tag
     * @param string $tag
     * @return $this
     */
    public function tag($tag = null)
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * 设置当前缓存项的有效期
     * @param mixed $expire
     * @return $this
     */
    public function expire($expire)
    {
        if (is_null($expire)) {
            $this->expire = null;
        } elseif (is_numeric($expire) || $expire instanceof \DateInterval) {
            $this->expiresAfter($expire);
        } elseif ($expire instanceof \DateTimeInterface) {
            $this->expire = $expire;
        } else {
            throw new \InvalidArgumentException('not support datetime');
        }
        
        return $this;
    }

    /**
     * 设置当前缓存项的准确过期时间点
     * @param \DateTimeInterface $expiration
     * @return $this
     */
    public function expiresAt($expiration)
    {
        if ($expiration instanceof \DateTimeInterface) {
            $this->expire = $expiration;
        } else {
            throw new \InvalidArgumentException('not support datetime');
        }
        
        return $this;
    }

    /**
     * 设置当前缓存项的的过期时间
     * @param int|DateInterval $timeInterval
     * @return $this
     */
    public function expiresAfter($timeInterval)
    {
        if ($timeInterval instanceof \DateInterval) {
            $this->expire = (int) \DateTime::createFromFormat('U', time())->add($timeInterval)->format('U');
        } elseif (is_numeric($timeInterval)) {
            $this->expire = $timeInterval + time();
        } else {
            throw new \InvalidArgumentException('not support datetime');
        }
        
        return $this;
    }
}