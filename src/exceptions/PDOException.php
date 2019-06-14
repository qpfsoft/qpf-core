<?php
namespace qpf\exceptions;

/**
 * PDO异常
 */
class PDOException extends DbException
{
    /**
     * 构造函数
     * @param \PDOException $exception pdo异常对象
     * @param array $config 数据库连接配置
     * @param string $sql 查询的sql语句
     * @param int $code
     */
    public function __construct(\PDOException $exception, array $config, $sql, $code = 10501)
    {
        $error = $exception->errorInfo;
        $this->setInfo('PDO Error', [
            'sql'       => $error[0],
            'code'      => isset($error[1]) ? $error[1] : 0,
            'message'   => isset($error[2]) ? $error[2] : '',
        ]);
        
        parent::__construct($exception->getMessage(), $config, $sql, $code);
    }
    
    /**
     * 获取异常名称
     *
     * @return string 返回字符串描述的该异常名称
     */
    public function getName()
    {
        return 'PDO Exception';
    }
}