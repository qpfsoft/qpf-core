<?php
namespace qpf\htmc\input;

/**
 * html5 date 时间拾取器控件
 * 
 * - input元素, 时间日期类型 : 
 * date, month, week, time, datetime, datetime-local;
 * 详解:
 * - `date` : 年月日, 显示效果`2017/10/21`, 提交值`2017-10-21`, 可用
 * - `datetime` : 定义 date 和 time 控件（带有时区）, 目前支持最差
 * - `datetime-local` : 定义 date 和 time 控件（不带时区）, 显示效果`2017/10/21 -- --:-- --:--`, 时间需要用上下箭头调整
 * - `month` : 年月, 显示效果`2017年10月`, 提交值`2017-10`, 可用
 * - `week` : 年周, 显示效果`2017年 第 42 周`, 提交值`2017-W42`, 可用
 * - `time` : 时:分, 显示效果`下午 ` 提交值`23:59`, 可用
 * 简介:
 * date - 选取日、月、年
 * month - 选取月、年
 * week - 选取周和年
 * time - 选取时间（小时和分钟）
 * datetime - 选取时间、日、月、年（UTC 时间）
 * datetime-local - 选取时间、日、月、年（本地时间）
 * 
 * @author qiun
 *
 */
class DatePickers extends InputBase
{
    /**
     * 是否启用自动完成功能 - h5
     *
     * - 根据用户历史输入, 会进行提示.
     * @param string $value 可能的值:
     * - `on` : 默认, 启用自动完成功能.
     * - `off`: 禁用自动完成功能.
     * @return $this
     */
    public function autocomplete($value)
    {
        $this->attr(['autocomplete' => $value]);
        return $this;
    }
    
    /**
     * 关闭原生输入验证 - h5
     *
     * - 覆盖form元素的`novalidate`属性
     * @return $this
     */
    public function formNovalidate()
    {
        $this->attr(['formnovalidate' => 'formnovalidate']);
        return $this;
    }
    
    /**
     * 输入允许的最大值 - h5
     * 
     * - max属性与min属性配合, 可创建合法值范围
     * @param string|integer $value 数字值,日期
     * @return $this
     */
    public function max($value)
    {
        $this->attr(['max' => $value]);
        return $this;
    }
    
    /**
     * 输入运行的最小值 - h5
     * - min属性与max属性配合, 可创建合法值范围
     * @param string|integer $value 数字值,日期
     * @return $this
     */
    public function min($value)
    {
        $this->attr(['max' => $value]);
        return $this;
    }
    
    /**
     * 规定输入的合法数值间隔 - h5
     *
     * 例`step="3"`, 合法范围[..-3、0、3、6..];
     *
     * - step 属性可以与 max 以及 min 属性配合使用，以创建合法值的范围。
     * @param string $value 间隔值
     * @return $this
     */
    public function step($value)
    {
        $this->attr(['step' => $value]);
        return $this;
    }
}