<?php
namespace qpf\base;

/**
 * 数组对象
 */
class ArrayObject implements ArrayObjectInterface
{
    /**
     * 源数组
     * @var array
     */
    protected $array = [];
    
    /**
     * 构造函数
     * @param array $arrays
     */
    public function __construct(array $arrays = [])
    {
        $this->replace($arrays);
    }
    
    /**
     * 添加元素
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->array[$key] = $value;
    }

    /**
     * 获取元素
     * @param string $key
     * @param mixed $default 元素不存在时返回的默认值
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->array[$key] : $default;
    }

    /**
     * 替换数组数据
     * @param array $arrays 该数组将覆盖源数组
     * @return void
     */
    public function replace(array $arrays)
    {
        foreach ($arrays as $key => $value) {
            $this->set($key, $value);
        }
    }

     /**
     * 返回数组数据
     * @return array
     */
    public function all()
    {
        return $this->array;
    }

     /**
     * 判断元素是否存在
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->array);
    }
    
    /**
     * 返回所有键名组成的数组
     * @return array
     */
    public function keys()
    {
        return array_keys($this->array);
    }

    /**
     * 移除元素
     * @param string $key
     * @return void
     */
    public function remove($key)
    {
        unset($this->array[$key]);
    }

    /**
     * 清空数组元素
     * @return void
     */
    public function clear()
    {
        $this->array = [];
    }

    /**
     * 统计数组元素数量
     * @return int
     */
    public function count()
    {
        return count($this->array);
    }

    /**
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->array);
    }
}