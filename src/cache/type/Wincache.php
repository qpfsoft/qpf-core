<?php
namespace qpf\cache\type;

use qpf\cache\CacheHandler;

class Wincache extends CacheHandler
{
    protected $options = [
        'prefix'     => '',
        'expire'     => 0,
        'serialize'  => true,
        'tag_prefix' => 'tag_',
    ];
    
    /**
     * 构造函数
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (!function_exists('wincache_ucache_info')) {
            throw new \BadFunctionCallException('not support: WinCache');
        }
        
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
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

        return wincache_ucache_inc($key, $step);
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

        return wincache_ucache_dec($key, $step);
    }

    /**
     * 删除缓存
     * @param string $name 缓存变量名
     * @return bool
     */
    public function rm($name)
    {
        $this->writeTimes++;

        return wincache_ucache_delete($this->getCacheKey($name));
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

        $key = $this->getCacheKey($name);

        return wincache_ucache_exists($key) ? $this->unserialize(wincache_ucache_get($key)) : $default;
    }

    /**
     * 写入缓存
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

        $key    = $this->getCacheKey($name);
        $expire = $this->getExpireTime($expire);
        $value  = $this->serialize($value);

        if (!empty($this->tag) && !$this->has($name)) {
            $first = true;
        }

        if (wincache_ucache_set($key, $value, $expire)) {
            isset($first) && $this->setTagItem($key);
            return true;
        }

        return false;
    }

    /**
     * 清除缓存
     * @return bool
     */
    public function clear(): bool
    {
        if (!empty($this->tag)) {
            foreach ($this->tag as $tag) {
                $this->clearTag($tag);
            }
            return true;
        }

        $this->writeTimes++;
        return wincache_ucache_clear();
    }

    /**
     * 判断缓存
     * @param string $name 缓存变量名
     * @return bool
     */
    public function has($name)
    {
        $this->readTimes++;

        $key = $this->getCacheKey($name);

        return wincache_ucache_exists($key);
    }
    
    /**
     * 清除指定tag的缓存
     * @param string $tag
     */
    public function clearTag($tag)
    {
        $keys = $this->getTagItems($tag);
        
        wincache_ucache_delete($keys);
        
        $tagName = $this->getTagkey($tag);
        $this->rm($tagName);
    }
}