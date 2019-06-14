<?php
namespace qpf\response;

use qpf\exception\ParamException;

class JsonResponse implements ResponseInterface
{
    /**
     * 内容类型
     * @var string
     */
    protected $contentType = 'text/html';
    /**
     * 处理选项
     * @var array
     */
    protected $options = [
        'json_encode_param' => JSON_UNESCAPED_UNICODE,
    ];
    
    /**
     * 格式化为当前输出类型
     * @param Response $response
     * @return void
     */
    public function output($response)
    {
        try {
            $data = $response->getData();
            $data = json_encode($data, $this->options['json_encode_param']);
            
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