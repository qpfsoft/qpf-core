<?php
namespace qpf\htmc\qcss;

/**
 * 选择符与css样式调度器
 * 
 * 
 * 连贯查询的方式生成css
 * 
 *  // 概念qcss
        //$aa = $h5->setStyle()->qId('div1')->start()->layout()->width("10px");
 * 
 * @author qiun
 *
 */
class Qcss
{
    /**
     * 选择符
     * @var string
     */
    private static $selector = '';
    /**
     * css生成的样式
     * @var string
     */
    private static $cssBuild = '';
    
    /**
     * 选择符列表
     * @var array
     */
    private static $_selector = [];
    
    /**
     * 添加选择符
     * @param string $selector
     */
    public function __construct()
    {
    }
    
    /**
     * 添加选择符
     * @param string $selector
     */
    public function add($selector)
    {
        self::$_selector[$selector] = []; 
    }
    
    public function start()
    {
        return;
    }
    
    /**
     * 获得指定选择符的css样式段落
     * @return string
     */
    public function end()
    {
        return 'ok';
    }
}