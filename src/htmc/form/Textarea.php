<?php
namespace qpf\htmc;

/**
 * 文本区域
 * 
 * @author qiun
 *        
 */
class Textarea
{

    public $name = '';

    /**
     * 样式类
     * 
     * @var string
     */
    public $class;

    /**
     * 文本区域高度
     *
     * @var int 指定在文本区域中可见的行数
     */
    public $rows = null;

    /**
     * 文本区域宽度
     *
     * @var int 指定文本框的宽度。默认为20
     */
    public $cols = null;

    /**
     * 限制输入字符数
     * 
     * @var int
     */
    public $maxLength = null;

    /**
     * 是否自动换行
     *
     * - off ： 不自动换行
     * - hard ：自动硬回车换行，换行标记一同被提交
     * - soft： 自动软回车换行，换行标记不会被提交
     * 
     * @var string
     */
    public $wrap = null;

    /**
     * 文本区域中的内容
     *
     * 文本区域没有value属性，设置的值直接放入到标签中间
     * 
     * @var string
     */
    public $value = null;

    /**
     * 鼠标经过的提示信息
     * 
     * @var string
     */
    public $title = null;

    /**
     * 未输入内容时，输入区域显示的提示信息
     * 
     * @var string
     */
    public $placeholder = null;

    /**
     * 原生验证 - 必须输入值
     *
     * < input required>
     * 
     * @var boolean
     */
    public $required = null;

    /**
     * 元素是否自动获得焦点
     *
     * 不适用于type="hidden"
     * < input autofocus="autofocus">
     * 
     * @var string
     */
    public $autofocus = null;

    /**
     * 元素是否禁用
     *
     * 表单中被禁用的 < input disabled> 元素不会被提交
     * 
     * @var boolean
     */
    public $disabled = null;

    /**
     * 规定输入字段是只读的
     *
     * 只读字段是不能修改的，但是可以复制值。
     * < input readonly>
     * 
     * @var boolean
     */
    public $readonly = null;
}