<?php
namespace qpf\console\command;

use qpf\base\Application;

/**
 * 命令抽象类
 */
abstract class Cmd
{
    /**
     * 应用程序
     * @var Application
     */
    protected $app;
    
    /**
     * 构造函数
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
    
    /**
     * 命令执行
     */
    abstract function run();
    
    /**
     * 获取命令帮助信息
     * @param string $method 可选, 指定方法
     * @return array
     */
    public function help($method = '')
    {
        if(empty($method)) {
            return get_obj_methods($this);
        }
        
        if(method_exists($this, $method)) {
            $reflect = new \ReflectionMethod($this, $method);
            return 'php qpf '. $method .' '. $this->parseMethodToString($reflect->__toString());
        }
        
    }
    
    /**
     * 格式化方法参数说明
     * ```
     * [* $opt] [* $param] [? $end = \'\']
     * ```
     * 星号代表必选参数, 问号代表可选参数
     * @param string $string
     * @return array
     */
    private function parseMethodToString($string)
    {
        $pos = strpos($string, '- Parameters [');
        $string = substr($string, $pos);
        
        $string = str_replace(['<', '>', ' '], ['*', '*', '!'], $string);
        $string = str_replace(['!!', '!'], ['', ' '], $string);
        $string = explode("\n", $string);
        array_shift($string);array_pop($string);array_pop($string);array_pop($string);
        $result = '';
        foreach ($string as $i => $str) {
            $arr = explode(' ', $str);
            $opt = '[';
            
            // 参数依赖性
            if(isset($arr['3'])) {
                if($arr['3'] == '*required*') {
                    $opt .= '* ';
                } else {
                    $opt .= '? ';
                }
            }
            
            // 变量名
            if(isset($arr['4'])) {
                $opt .= $arr['4'];
            }
            
            // 是否有默认值
            if(isset($arr['5']) && $arr['5'] == '=') {
                $opt .= ' = '.$arr['6'];
            }
            
            $opt .= '] ';
            $result .= $opt;
        }
        
        return $result;
    }
}