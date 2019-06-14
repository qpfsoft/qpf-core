<?php
use qpf\deunit\TestUnit;
use qpf\web\Http;
use qpf\base\Application;
use qpf\web\Request;

include __DIR__ . '/../boot.php';

/**
 * 测试HTTP处理程序
 * 
 * - 入口绑定
 *      |-  域名: 进入单应用运行模式
 *      |-  别名: 进入单应用运行模式
 * - 路由解析
 * 
 * 
 */
class HttpTest2 extends TestUnit
{
    public $http;
    public $app;
    
    public function setUp()
    {
        $this->app = new Application();
        $this->app->init();
        $this->http = new Http($this->app);
    }
    
    /**
     * 提交请求
     * @param string $url
     * @param array $server
     * @return Request
     */
    public function submit($url, array $server = [])
    {
        $method = 'GET';
        $params = [];
        $cookie = [];
        $files = [];
        $server = array_merge($_SERVER, $server);
        
        $request = new Request($this->app);
        $request->submit($url, $method, $params, $cookie, $files, $server);
        // 覆盖原始请求类
        $this->app->instance('request', $request);
        return $request;
    }
    
    
    
    /**
     * 绑定子域名到指定模块
     * 
     * - 绑定后, 将会是一个单应用模式运行
     */
    public function testBase()
    {
        $request = $this->submit('http://api.domain.com', [
            'HTTP_HOST' => 'api.domain.com',
        ]);

        $this->http->bindDomain('api', 'foo2');
        
        return $this->http->testRequestRoute($request);
    }
    
    
}

echor(HttpTest2::runTestUnit());