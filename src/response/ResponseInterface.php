<?php
namespace qpf\response;

/**
 * 响应类型格式化接口
 */
interface ResponseInterface
{
    /**
     * 格式化为当前输出类型
     * @param Response $response
     * @return void
     */
    public function output($response);
}