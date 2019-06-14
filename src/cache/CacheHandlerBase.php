<?php
namespace qpf\cache;

use qpf\psr\simple_cache\CacheInterface;

/**
 * 缓存处理程序基类
 */
abstract class cacheHandlerBase implements CacheInterface
{

    /**
     * 自增缓存 - 针对数值缓存
     * @param string $name 缓存变量名
     * @param int $step 步长
     * @return false|int
     */
    abstract public function inc($name, $step = 1);

    /**
     *
     * 自减缓存 - 针对数值缓存
     * @param string $name 缓存变量名
     * @param int $step 步长
     * @return false|int
     */
    abstract public function dec($name, $step = 1);

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return bool
     */
    abstract public function rm($name);

    /**
     * 删除缓存
     * @param string $name 缓存变量名
     * @return bool
     */
    public function delete($key)
    {
        return $this->rm($key);
    }

    /**
     * 读取多个缓存
     * @param iterable $keys 缓存变量名
     * @param mixed $default 默认值
     * @return iterable
     * @throws InvalidArgumentException
     */
    public function getMultiple($keys, $default = null)
    {
        $result = [];
        
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }
        
        return $result;
    }

    /**
     * 写入缓存
     * @access public
     * @param iterable $values 缓存数据
     * @param null|int|\DateInterval $ttl 有效时间 0为永久
     * @return bool
     */
    public function setMultiple($values, $ttl = null)
    {
        foreach ($values as $key => $val) {
            $result = $this->set($key, $val, $ttl);
            
            if (false === $result) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * 删除缓存
     * @param iterable $keys 缓存变量名
     * @return bool
     * @throws InvalidArgumentException
     */
    public function deleteMultiple($keys)
    {
        foreach ($keys as $key) {
            $result = $this->delete($key);
            
            if (false === $result) {
                return false;
            }
        }
        
        return true;
    }
}