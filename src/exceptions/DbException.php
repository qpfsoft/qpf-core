<?php
namespace qpf\exceptions;

/**
 * 数据库异常
 */
class DbException extends Exception
{
    /**
     * 构造函数
     * @param string $message
     * @param array $config
     * @param string $sql
     * @param int $code
     */
    public function __construct($message, array $config, $sql, $code = 10500)
    {
        $this->message = $message;
        $this->code = $code;
        $this->setInfo('Db Error', [
            'code'      => $code,
            'message'   => $message,
            'sql'       => $sql
        ]);
        
        unset($config['user'], $config['pwd']);
        $this->setInfo('Db Config', $config);
    }
    
    /**
     * 获取异常名称
     *
     * @return string 返回字符串描述的该异常名称
     */
    public function getName()
    {
        return 'Database Exception';
    }
    
    /**
     * 异常可读的形式
     * @return string
     */
    public function __toString()
    {
        return parent::__toString() . PHP_EOL . ' Additional Information:' . PHP_EOL . print_r($this->errorInfo, true);
    }
}