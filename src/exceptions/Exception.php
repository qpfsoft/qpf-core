<?php
namespace qpf\exceptions;

/**
 * QPF Exception
 */
class Exception extends \Exception
{
    /**
     * 调试信息
     * @var array
     */
    protected $info;
    
    /**
     * 记录调试信息
     * @param string $type 消息分类标识
     * @param array $data 数据信息, 键值对形式.
     */
    final public function setInfo($type, $data)
    {
        if(isset($this->info[$type])) {
            $data = array_merge($this->info[$type], $data);
        }
        $this->info[$type] = $data;
    }
    
    /**
     * 返回调试信息
     * @param string $type 消息分类标识
     * @return array
     */
    final public function getInfo($type = null)
    {
        if($type !== null) {
            return isset($this->info[$type]) ? $this->info[$type] : null;
        }
        return $this->info;
    }
    
    /**
     * 获取异常名称
     * @return string
     */
    public function getName()
    {
        return 'Exception';
    }
}