<?php
namespace qpf\cache;

use qpf;

/**
 * 缓存处理程序操作基类
 */
abstract class CacheHandler extends CacheHandlerBase
{

    /**
     * 具体类型的缓存处理对象
     * @var object
     */
    protected $handler;

    /**
     * 缓存读取次数
     * @var integer
     */
    protected $readTimes = 0;

    /**
     * 缓存写入次数
     * @var integer
     */
    protected $writeTimes = 0;

    /**
     * 缓存参数
     * @var array
     */
    protected $options = [];

    /**
     * 缓存标签
     * @var array
     */
    protected $tag;

    /**
     * 序列化方法
     * ```
     * ['序列化回调方法', '反序列化回调方法', 序列参数, 反序列化参数]
     * ```
     * @var array
     */
    protected static $serialize = ['\qpf\func\Serialization::serialize','\qpf\func\Serialization::unserialize','qpf_serialize:',16];

    /**
     * 获取有效期
     * @param integer|\DateTimeInterface $expire 有效期
     * @return int
     */
    protected function getExpireTime($expire)
    {
        if ($expire instanceof \DateTimeInterface) {
            $expire = $expire->getTimestamp() - time();
        }
        
        return (int) $expire;
    }

    /**
     * 获取实际的缓存标识
     * @param string $name 缓存名
     * @return string
     */
    protected function getCacheKey($name)
    {
        return $this->options['prefix'] . $name;
    }

    /**
     * 读取缓存并删除
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function pull($name)
    {
        $result = $this->get($name, false);
        
        if ($result) {
            $this->rm($name);
            return $result;
        }
    }

    /**
     * 如果不存在则写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value 存储数据
     * @param int $expire 有效时间 0为永久
     * @return mixed
     */
    public function remember($name, $value, $expire = null)
    {
        if ($this->has($name)) {
            return $this->get($name);
        }
        
        $time = time();
        
        while ($time + 5 > time() && $this->has($name . '_lock')) {
            // 存在锁定则等待
            usleep(200000);
        }
        
        try {
            // 锁定
            $this->set($name . '_lock', true);
            
            if ($value instanceof \Closure) {
                // 获取缓存数据
                $value = QPF::container()->callFunc($value);
            }
            
            // 缓存数据
            $this->set($name, $value, $expire);
            
            // 解锁
            $this->rm($name . '_lock');
        } catch (\Exception | \throwable $e) {
            $this->rm($name . '_lock');
            throw $e;
        }
        
        return $value;
    }

    /**
     * 缓存标签
     * @param string|array $name 标签名
     * @return $this
     */
    public function tag($name)
    {
        if ($name) {
            $this->tag = (array) $name;
        }
        
        return $this;
    }

    /**
     * 更新标签
     * @param string $name 缓存标识
     * @return void
     */
    protected function setTagItem($name)
    {
        if (!empty($this->tag)) {
            $tags = $this->tag;
            $this->tag = null;
            
            foreach ($tags as $tag) {
                $key = $this->getTagKey($tag);
                
                if ($this->has($key)) {
                    $value = explode(',', $this->get($key));
                    $value[] = $name;
                    
                    if (count($value) > 1000) {
                        array_shift($value);
                    }
                    
                    $value = implode(',', array_unique($value));
                } else {
                    $value = $name;
                }
                
                $this->set($key, $value, 0);
            }
        }
    }

    /**
     * 获取标签包含的缓存标识
     * @param string $tag 缓存标签
     * @return array
     */
    protected function getTagItems($tag)
    {
        $key = $this->getTagkey($tag);
        $value = $this->get($key);
        
        if ($value) {
            return array_filter(explode(',', $value));
        } else {
            return [];
        }
    }

    /**
     * 获取标签的键名
     * @param string $tag
     * @return string
     */
    protected function getTagKey($tag)
    {
        return $this->options['tag_prefix'] . md5($tag);
    }

    /**
     * 序列化数据
     * @param mixed $data
     * @return string
     */
    protected function serialize($data)
    {
        if (is_scalar($data) || ! $this->options['serialize']) {
            return $data;
        }
        
        $serialize = self::$serialize[0];
        
        return self::$serialize[2] . $serialize($data);
    }
    
    /**
     * 反序列化数据
     * @param  string $data
     * @return mixed
     */
    protected function unserialize($data)
    {
        if ($this->options['serialize'] && 0 === strpos($data, self::$serialize[2])) {
            $unserialize = self::$serialize[1];
            return $unserialize(substr($data, self::$serialize[3]));
        }
        
        return $data;
    }
    
    /**
     * 注册序列化机制
     * @param callable $serialize 序列化方法
     * @param callable $unserialize 反序列化方法
     * @param string $prefix 序列化前缀标识
     * @return void
     */
    public static function registerSerialize(callable $serialize, callable $unserialize, string $prefix = 'qpf_serialize:')
    {
        self::$serialize = [$serialize, $unserialize, $prefix, strlen($prefix)];
    }
    
    /**
     * 返回当前缓存操作对象
     * @return object
     */
    public function handler()
    {
        return $this->handler;
    }
    
    /**
     * 返回缓存读取次数
     * @return int
     */
    public function getReadTimes()
    {
        return $this->readTimes;
    }
    
    /**
     * 缓存读取次数
     * @return int
     */
    public function getWriteTimes()
    {
        return $this->writeTimes;
    }
}