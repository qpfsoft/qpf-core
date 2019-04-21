<?php
namespace qpf\exceptions;

/**
 * 参数异常
 * 
 * 调用方法或函数时, 缺少参数, 参数值不符合定义或不是预期类型的异常
 */
class ParameterException extends \LogicException
{
    /**
     * 无效类型
     * @var string
     */
    protected $invalid = '';
    
    /**
     * 参数类型无效
     * ```
     * invalidType(1, 'int'); // 参数1类型错误, 期望类型int
     * invalidType('$name', 'string'); // 参数$name类型错误, 期望类型string
     * ```
     * @param string $var 参数名或索引位置
     * @param string $expect 期望类型描述
     * @return void
     */
    public function invalidType($var, $expect = '')
    {
        $this->invalid = 'Type';
        
        $this->message = 'parame `' . (string) $var . '` type error';
        
        if(!empty($expect)) {
            $this->message .= ' , Expected type `' . (string) $expect . '`';
        }
    }
    
    /**
     * 无效参数长度
     * @param mixed $var 传入参数
     * @param string $rule 规则描述
     * @return void
     */
    public function invalidLength($var, $rule = '')
    {
        $this->invalid = 'Length';
        
        if (is_string($var)) {
            $len = strlen($var);
        } elseif (is_array($var)) {
            $len = count($var);
        } else {
            $len = 'unknown';
        }
        
        $this->message = 'input length : `' . $len . '`';
    }
    
    /**
     * 无效数组索引
     * @param string $var 索引id
     */
    public function invalidArrayIndex($var = '')
    {
        $this->message = 'invalid array index ' . $var;
    }
    
    /**
     * 无效数组键名
     * @param string $var 键名
     */
    public function invalidArrayKey($var = '')
    {
        $this->message = 'invalid array key name ' . $var;
    }

    /**
     * 无效参数范围
     * @param string $var 范围描述
     */
    public function invalidBetween($var = '')
    {
        $this->message = 'invalid parameter range ' . $var;
    }
    
    /**
     * 获取异常名称
     * @return string
     */
    public function getName()
    {
        return 'Invalid Parameter ' . $this->invalid;
    }
}