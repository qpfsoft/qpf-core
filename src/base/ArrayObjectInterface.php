<?php
namespace qpf\base;

/**
 * ArrayObjectInterface
 * 
 * ArrayAccess 用于将访问对象提供为数组的接口;
 * Countable 用于实现统计对象数组数量的接口;
 * IteratorAggregate 用于创建外部迭代器的接口;
 */
interface ArrayObjectInterface extends \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * 添加元素
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value);
    
    /**
     * 获取元素
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null);
    
    /**
     * 替换数组数据
     * @param array $arrays
     */
    public function replace(array $arrays);
    
    /**
     * 返回数组数据
     * @return array
     */
    public function all();
    
    /**
     * 判断元素是否存在
     * @param string $key
     * @return bool
     */
    public function has($key);
    
    /**
     * 移除元素
     * @param string $key
     */
    public function remove($key);
    
    /**
     * 清除数组
     */
    public function clear();
}