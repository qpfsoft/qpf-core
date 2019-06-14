<?php
namespace qpf\builder\template;

use qpf\builder\code\ArrayCode;

/**
 * 生成PHP数组配置单文件
 * 
 * 通过[[setConfig($arr)]]传入数组数据, 再调用[[getContent()]]即可获得生成文件内容!
 */
class ArrayConfigFile extends PhpFile
{
    /**
     * 预设配置项
     * @var array
     */
    public $config = [];
    
    /**
     * 获取文件内容
     * @return string
     */
    public function getContent():string
    {
        $this->content =  'return ' . ArrayCode::build($this->config) . ';';
        return parent::getContent();
    }
    
    /**
     * 设置配置数组
     * @param array $array
     */
    public function setConfig(array $array)
    {
        $this->config = $array;
    }
}