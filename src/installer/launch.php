<?php
declare(strict_types=1);

namespace qpf\installer;

/**
 * 
 * @author QPF-Y410P
 *
 */
class launch
{
    /**
     * 检查生产环境
     */
    public function checkEnv()
    {
        // 错误级别
        \error_reporting(E_ALL);
        
        // 版本检查
        if (PHP_MAJOR_VERSION < 7) {
            exit('requires PHP 7.');
        }
    }
    
    public function run()
    {
        $this->checkEnv();
    }
}