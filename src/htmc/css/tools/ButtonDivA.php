<?php
namespace qpf\htmc\css\tools;

/**
 * DIV+A标签的按钮
 * 
 * 通过div的边框和背景来装饰按钮, a标签来实现文字和点击.
 * 
 * - 所有样式在a标签上, div只居中显示
 * 
 * @author qiun
 *
 */
class ButtonDivA extends ToolsBase
{
    /**
     * 配置数组
     * - 改变class名称
     * - 改变文本大小, 按钮内填充
     * @var array
     */
    public $config = [
        'class-div' => 'btn-box', // 包裹a标签的div的class名称
        'class-a'   => 'btn', // a标签-按钮的class名
        
        'text'      => '按钮文本', //a标签-按钮文本
        'btn-font-size'  => '14px', // 字体大小
        'btn-padding-tb'     => '4px', // 上下内填充
        'btn-padding-lr'    =>  '10px', // 左右内填充
    ];
    
    public function getCss()
    {
        $style = '';
        // div标签-按钮居中
        $style .= $this->createClass($this->config['class-div'], $this->css()->text()->text_align_center());
        // a标签-按钮基础样式
        $style .= $this->createClass(".{$this->config['class-a']}",
            $this->css()->background()->background_color('#0084CC'),
            $this->css()->layout()->display('inline-block'),
            //$this->css()->IE67_Hack .  $this->css()->layout()->display('inline'),
            $this->css()->layout()->padding2($this->config['btn-padding-tb'], $this->config['btn-padding-lr']),
            $this->css()->border()->border('1px', 'solid', '#cccccc'),
            // 边框透明?
            $this->css()->border()->border_color('rgba(0, 0, 0, 0.1)', 'rgba(0, 0, 0, 0.1)', 'rgba(0, 0, 0, 0.1)', 'rgba(0, 0, 0, 0.1)'),
            $this->css()->text()->color('#ffffff'),
            $this->css()->text()->font_size($this->config['btn-font-size']),
            $this->css()->text()->text_align_center(),
            $this->css()->text()->text_decoration_none(),
            // 按钮美化样式
            $this->css()->css2()->cursor('pointer'),
            $this->css()->border()->border_radius('6px'),
            $this->css()->text()->vertical_align_middle(),
            $this->css()->background()->background_image_linear_gradient('#0088cc', '#0055cc', 'top'),
            $this->css()->background()->background_repeat('repeat-x'),
            $this->css()->text()->text_shadow('0', '-1px', '0', 'rgba(0, 0, 0, 0.25)'));
        
        
        // a标签-按钮-鼠标指针悬浮状态
        $style .= $this->createClass(".{$this->config['class-a']}:hover",
            // 纯色背景颜色+背景颜色偏移
            $this->css()->background()->background_color('#0055cc'),
            $this->css()->background()->background_position('0', '-15px'),
            $this->css()->transition()->transitions('background-position', '0.1s', 'linear'));
        
        // a标签-按钮- 点击效果
        $style .= $this->createClass(".{$this->config['class-a']}:active",
            $this->css()->background()->background_color('#004ab3'),
            // 去除渐变的背景
            $this->css()->background()->background_image('none'),
            // 去除链接 虚线框
            $this->css()->border()->outline_none(),
            // 自动叠加色
            $this->css()->text()->color('rgba(255, 255, 255, 0.75)'),
            // 内阴影
            $this->css()->background()->box_shadow('0', '0', '2px', '4px', 'rgba(0, 0, 0, 0.15)', 'inset'));
        // a标签-获得焦点
        $style .= $this->createClass(".{$this->config['class-a']}:focus",
            $this->css()->border()->outline('thin', 'dotted', '#333'),
            $this->css()->border()->outline('5px', 'auto', '-webkit-focus-ring-color'),
            $this->css()->border()->outline_offset('-2px'));
        
        return $style;
    }
    
    public function getHtml()
    {
        return $this->htmc()->html()->div()->attr(['class'=> $this->config['class-div']])->content(
            $this->htmc()->tagA()->attr(['class'=> $this->config['class-a']])->url()->content($this->config['text'])->getHtml()
            )->getHtml();
    }
}