<?php
namespace qpf\response;

class HtmlResponse implements ResponseInterface
{
    /**
     * 内容类型
     * @var string
     */
    protected $contentType = 'text/html';
    
    /**
     * 格式化为当前输出类型
     * @param Response $response
     * @return void
     */
    public function output($response)
    {
        if(stripos($this->contentType, 'charset') === false) {
            $response->putContentType($this->contentType, $response->getCharset());
        } else {
            $response->setHeader('Content-Type', $this->contentType);
        }
        
        if ($response->getData() !== null) {
            $response->setContent($response->getData());
        }
    }
}