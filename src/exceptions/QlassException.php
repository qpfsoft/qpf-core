<?php
namespace qpf\exceptions;

/**
 * Qlass 对象定义配置异常
 */
class QlassException extends \LogicException
{
    /**
     * 类定义数组缺少指定元素
     * @param string $key 默认`class`元素
     * @return void
     */
    public function badConfig($key = 'class')
    {
        $this->message = 'Qlass definition config miss [`' . $key . '`] element';
        if($key == 'class') {
            $this->message .= ', set class name';
        }
    }
    
    /**
     * 未知的类定义类型
     * @param mixed $config 类的定义配置
     * @param string $id 类标识
     * @return void
     */
    public function badConfigType($config, $id = null)
    {
        $this->message = 'Qlass unknown config type : `' . gettype($config) . '`';
        
        if (func_num_args() > 1) {
            $this->message .= ', for `' . $id .  '`';
        }
    }
    
    /**
     * 获取异常名称
     * @return string
     */
    public function getName()
    {
        return 'Invalid Qlass Config';
    }
}