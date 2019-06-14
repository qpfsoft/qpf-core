<?php
namespace qpf\web;

/**
 * Url
 * 
 * 帮助生成Url地址.
 */
class Url
{
    /**
     * 解析请求
     * @param Request $request
     * @return array
     */
    public function parseRequest(Request $request)
    {
        $path = $request->path();
        
        return [$path, []];
    }
}