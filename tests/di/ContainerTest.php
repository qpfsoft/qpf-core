<?php
use qpf\deunit\TestUnit;
use qpf\base\Container;

include __DIR__ . '/../boot.php';

class Foo {
    public $var = 'show';
    
    public function get()
    {
        return __METHOD__;
    }
}

class ContainerTest extends TestUnit
{
    public $di;
    
    public function setUp()
    {
        $this->di = new Container();
    }
    
    /**
     * 向容器注册实例对象
     */
    public function testInstance()
    {
        try {
            $this->di->instance('foo', new Foo());
        } catch (\Exception $e) {
            return false;
        }
        
        return true;
    }
    
    public function testBind()
    {
        $this->di->bind('foo', [
            '$class' => '\Foo',
        ]);

        return $this->di->getBind('foo');
    }
    
    public function testBind2()
    {
        $this->di->bind('foo', '\Foo');
        
        return $this->di->getBind('foo');
    }
    
    public function testBinds()
    {
        $configs = [
            'foo' => '\Foo',
            'boo' => [
                '$class' => '\boo',
                '$params' => [],
                '$singlg' => true,
                'var1' => 'a',
            ],
        ];
        
        $this->di->binds($configs);
        
        return $this->di->getBinds();
    }

}

echor(ContainerTest::runTestUnit());