<?php
namespace qpf\response;

use qpf;
use qpf\exception\ParamException;

class JsonpResponse implements ResponseInterface
{
    /**
     * 内容类型
     * @var string
     */
    protected $contentType = 'application/javascript';
    /**
     * 处理选项
     * @var array
     */
    protected $options = [
        'var_jsonp_handler'     => 'callback',
        'default_jsonp_handler' => 'jsonpReturn',
        'json_encode_param'     => JSON_UNESCAPED_UNICODE,
    ];
    
    /**
     * 格式化为当前输出类型
     * @param Response $response
     * @return void
     */
    public function output($response)
    {
        try {
            $var_jsonp_handler = QPF::app()->response();
            $data = json_encode($response->getData(), $this->options['json_encode_param']);
            
            if ($data === false) {
                throw new ParamException(json_last_error_msg());
            }
        } catch (\Exception $e) {
            if ($e->getPrevious()) {
                throw $e->getPrevious();
            }
            throw $e;
        }
        
    }
}