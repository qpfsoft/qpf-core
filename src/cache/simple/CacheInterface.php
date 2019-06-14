<?php
namespace qpf\cache\simple;

/**
 * 简单缓存接口
 */
interface CacheInterface
{
    /**
     * 连接
     * @return mixed
     */
    public function connect();
    
    /**
     * 设置缓存
     * @param string $name 缓存标识
     * @param mixed $value 缓存值
     * @param int $expire 有效期
     * @return mixed
     */
    public function set($name, $value, $expire);
    
    /**
     * 获取缓存
     * @param string $name 缓存标识
     * @return mixed
     */
    public function get($name);
    
    /**
     * 删除缓存
     * @param string $name 缓存标识
     * @return mixed
     */
    public function delete($name);
    
    /**
     * 刷新缓存
     * @return mixed
     */
    public function flush();
}