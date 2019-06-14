<?php
namespace qpf\htmc\input;

class Form
{

    /**
     * 表单ID
     * 
     * @var string
     */
    public $id = 'form';

    /**
     * CSS类
     * 
     * @var string
     */
    public $class;

    /**
     * 表单提交URL
     * 
     * @var string
     */
    public $action = '';

    /**
     * 表单提交方式
     * 
     * @var string
     */
    public $method = [
        'post' => 'POST',
        'get' => 'GET'
    ];

    /**
     * 表单描述
     * 
     * @var string
     */
    public $title = null;

    /**
     * 是否使用HTML5原生表验证
     * 
     * @var boolean
     */
    public $novalidate = null;

    /**
     * 设置表单元素是否记录历史
     * 
     * @var string on/off 开启或关闭
     */
    public $autocomplete = null;

    /**
     * 表单数据编码类型
     *
     * 适用于 method="post" 的情况！
     * - application/x-www-form-urlencoded : 默认，发送到服务器之前，
     * 所有字符都会进行编码（空格转换为 "+" 加号，特殊符号转换为 ASCII HEX 值）
     * - multipart/form-data ： 不对字符编码。在使用包含文件上传控件的表单时，必须使用该值。
     * - text/plain : 不对特殊字符进行编码，只会将空格转为加号
     * 
     * @var String
     */
    public $enctype = [
        'default' => 'application/x-www-form-urlencoded',
        'upload' => 'multipart/form-data',
        'NotEncoded' => 'text/plain'
    ];

    /**
     * 表单提交到的URL页面打开方式
     *
     * - _blank : 在新窗口中打开被链接文档
     * - _self : 默认。在相同的框架中打开被链接文档
     * - _parent : 在父框架集中打开被链接文档
     * - _top : 在整个窗口中打开被链接文档
     * 
     * @var string
     */
    public $target = null;

    /**
     * 表单数据字符集
     * 
     * @var string UTF-8/ISO-8859-1
     */
    public $acceptCharset = null;
}
