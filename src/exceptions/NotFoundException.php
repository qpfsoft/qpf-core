<?php
namespace qpf\exceptions;

/**
 * 未找到或不支持的异常
 */
class NotFoundException extends \RuntimeException
{
    /**
     * 查找类型
     * @var string
     */
    protected $invalid = '';
    
    /**
     * 构造函数
     * @param string $message 查找内容描述
     * @param string $type 查找类型
     */
    public function __construct($message, $type = '')
    {
        if (!empty($type)) {
            $this->invalid = ucfirst($type);
            $this->message = $this->invalid . ' not exists : ' . $message;
        } else {
            $this->message = $message;
        }
    }
    
    /**
     * 获取异常名称
     * @return string
     */
    public function getName()
    {
        return 'Not Found ' . $this->invalid;
    }
}