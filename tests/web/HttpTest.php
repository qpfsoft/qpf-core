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
class HttpTest extends TestUnit
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
        
        $request = new Request();
        $request->submit($url, $method, $params, $cookie, $files, $server);
        // 覆盖原始请求类
        $this->app->instance('request', $request);
        return $request;
    }
    
    /**
     * 绑定模块别名
     * 
     * - 若域名绑定, 将不会识别别名解析
     */
    public function testIndex3()
    {
        $request = $this->submit('http://api.domain.com/user', [
            'HTTP_HOST' => 'api.domain.com',
        ]);
        
        $this->http->bindMap('user', 'foo3');
        
        return $this->http->testRequestRoute($request);
    }
    
    /**
     * 绑定模块别名
     *
     * - 若app模块名绑定了别名, 将不允许再使用原始名称访问模块.
     */
    public function testIndex4()
    {
        $request = $this->submit('http://api.domain.com/foo3', [
            'HTTP_HOST' => 'api.domain.com',
        ]);

        return $this->http->testRequestRoute($request);
    }
    
    /**
     * 绑定域名到指定模块
     * 
     * - 绑定后, 将会是一个单应用模式运行
     */
    public function testIndex1()
    {
        $request = $this->submit('http://abc.domain.com', [
           'HTTP_HOST' => 'abc.domain.com',
        ]);

        $this->http->bindDomain('abc.domain.com', 'foo');
        
        return $this->http->testRequestRoute($request);
    }
    
    /**
     * 绑定子域名到指定模块
     * 
     * - 绑定后, 将会是一个单应用模式运行
     */
    public function testIndex2()
    {
        $request = $this->submit('http://api.domain.com', [
            'HTTP_HOST' => 'api.domain.com',
        ]);

        $this->http->bindDomain('api', 'foo2');
        
        return $this->http->testRequestRoute($request);
    }
    
    
}

echor(HttpTest::runTestUnit());