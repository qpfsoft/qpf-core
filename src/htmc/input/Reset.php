<?php
namespace qpf\htmc\input;

/**
 * Input元素`reset`重置按钮类型
 * 
 * 定义重置按钮。重置按钮会清除表单中的所有数据。
 * @author qiun
 *        
 */
class Reset extends Button
{
    /**
     * 规定input元素的类型
     * @var string
     */
    protected $inputType = 'reset';
}