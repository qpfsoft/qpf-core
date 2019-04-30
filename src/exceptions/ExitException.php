<?php
namespace qpf\exceptions;

/**
 * 退出异常
 * 
 * 该异常为应用程序正常退出, 不要捕捉该异常
 */
class ExitException extends \Exception
{
    /**
     * 退出状态码
     * @var integer
     */
    public $statusCode;
    
    /**
     * 构造函数
     * @param integer $status 退出状态码
     * @param string $message 错误信息
     * @param integer $code 错误码
     * @param \Exception $previous 异常链中的前一个异常
     */
    public function __construct($status = 0, $message = null, $code = 0, \Exception $previous = null)
    {
        $this->statusCode = $status;
        parent::__construct($message, $code, $previous);
    }
}