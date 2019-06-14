<?php
declare(strict_types = 1);

namespace qpf\safety;

/**
 * GUID 是微软对UUID标准的一种实现
 * 
 * 一个命名空间内生成的与另外一个命名空间生成的不会重复, 用于项目隔离!
 */
class Guid
{
    /**
     * 生成guid
     * @param string $namespace 命名空间
     * @param bool 是否uuid格式, 默认`true`
     * @return string
     */
    public static function id(string $namespace = '', bool $uuid = true): string
    {
        static $guid = '';
        $uid = uniqid('', true);
        $data = $namespace;
        $data .= isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true);
        $data .= isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $data .= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
        $data .= isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : '';
        $data .= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $data .= isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : '';
        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
        $guid = substr($hash,  0,  8) . '-' .
            substr($hash,  8,  4) . '-' .
            substr($hash, 12,  4) . '-' .
            substr($hash, 16,  4) . '-' .
            substr($hash, 20, 12);
            
            return $uuid ? $guid : '{' . $guid . '}';
    }
    
    /**
     * uniqid模拟
     * @return string
     */
    private function _encode()
    {
        $m = microtime(true);
        return sprintf("%8x%05x\n",floor($m),($m-floor($m))*1000000);
    }
    
    /**
     * uniqid复原
     * @return string
     */
    private function _decode()
    {
        return date("Y-m-d h:i:s",hexdec(substr(uniqid(),0,8)));
    }
}