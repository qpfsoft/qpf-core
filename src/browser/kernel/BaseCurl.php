<?php
namespace qpf\browser\kernel;

use qpf\exception\NotFoundException;

/**
 * 
 * # 需求
 * 需要安装libcurl包才能使用 PHP 的 cURL 函数。
 * PHP 需要使用 7.10.5 或更高版本的 libcurl。
 * 
 */
class BaseCurl extends Base
{
    public function init()
    {
        if (!function_exists('curl_init')) {
            throw new NotFoundException('curl not found', 'Extension');
        }
    }
    
    /**
     * 禁止克隆对象
     */
    public function __clone()
    {
        throw new CurlException('Cannot clone object');
    }
    
    public function request($method, $url)
    {
        
    }
    
    public function get($url, $params = [])
    {
        
    }
    
    public function post($url, $params = [])
    {
        
    }
    
    public function exec()
    {
        
    }
}