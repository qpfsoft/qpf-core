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

class ContainerTest2 extends TestUnit
{
    public $di;
    
    public function setUp()
    {
        $this->di = new Container();
    }
    

    public function testBase1()
    {
        $this->di->bind('foo', function(){
            return new \Foo();
        });
        
        echor($this->di);
    }

    public function testBase2()
    {
        return $this->di->get('foo');
    }
}

echor(ContainerTest2::runTestUnit());