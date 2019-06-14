<?php
use qpf\deunit\TestUnit;
use qpf\base\PathAlias;
use qpf\base\Apaths;

include __DIR__ .'/../boot.php';

/**
 * 别名路径
 */
class ApathsTest extends TestUnit
{
    public $apth;
    public $prefix;
    
    public function setUp()
    {
        $this->apth = new Apaths();
        $this->apth->prefix = '@';
        $this->prefix = $this->apth->prefix;
    }
    
    /**
     * 设置别名路径
     */
    public function testSetAlias()
    {
        $this->apth->setAlias('root', __DIR__);
        $this->apth->setAliases([
            'www'   => $this->prefix . 'root/www',
            $this->prefix . 'root/public'  => $this->prefix . 'root/www/public',
            $this->prefix . 'root/static'  => $this->prefix . 'root/www/static',
        ]);
        
        return $this->apth->getAliases();
    }
    
    /**
     * 获取别名
     */
    public function testGetAlias1()
    {
        $value = $this->apth->getAlias($this->prefix . 'www');
        
        return $this->where($value, '=', strtr(__DIR__, '\\', '/') . '/www');
    }
    
    /**
     * 解析路径中的别名
     * @return boolean
     */
    public function testGetAlias2()
    {
        $value = $this->apth->getAlias($this->prefix . 'root/static/image/logo.png');

        return $this->where($value, '=', strtr(__DIR__, '\\', '/') . '/www/static/image/logo.png');
    }
    
    /**
     * 获取路径中别名部分
     */
    public function testGetPathAlias()
    {
        $value = $this->apth->getPathAlias($this->prefix . 'root/static/image/logo.png');
        
        return $this->where($value, '=', $this->prefix . 'root/static');
    }
    
    /**
     * 检擦给定的路径是否包含别名路径
     * @return boolean
     */
    public function testIsAlias()
    {
        return [
            'root/asb' => $this->apth->isAlias('root/asb'),
            $this->prefix .'root/asb' => $this->apth->isAlias($this->prefix . 'root/asb')
        ];
    }
}

echor(ApathsTest::runTestUnit());