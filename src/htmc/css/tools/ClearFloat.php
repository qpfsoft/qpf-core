<?php
namespace qpf\htmc\css\tools;

/**
 * 清除浮动(Float)工具
 * 
 * #浮动:
 * Float : left;
 * 定义：使元素脱离文档流，按照指定方向发生移动，遇到父级边界或者相邻的浮动元素停了下来。
 * #问题: 高度塌陷
 * 浮动元素父元素高度自适应（父元素不写高度时，子元素写了浮动后，父元素会发生高度塌陷）
 * - 解决1: 为父元素手动指定高度
 * - 解决2: 清除浮动
 * clear:left | right | both | none | inherit：元素的某个方向上不能有浮动元素 
 * clear:both：在左右两侧均不允许浮动元素。
 * 
 * 方案1:
 * - 在浮动的元素后面创建一个空div, 设置样式{clear:both;height:0;overflow:hidden;}
 * 
 * 方案2:
 * - 需要给每个浮动元素父级添加浮动，浮动多了容易出现问题
 * 
 * 方案3:
 * - 父级设置成inline-block, 缺点：父级的margin左右auto失效，无法使用margin: 0 auto;居中了
 * 
 * 方案4:
 * - br 清浮动与空div类似,不过br 标签自带clear属性, <br clear="both" />
 * 
 * 方案5:
 * - 给父级添加overflow:hidden 清浮动方法, zoom 用来兼容IE6 IE7{overflow: hidden;*zoom: 1;}
 * 
 * 方案6: 推荐
 * - after伪类 清浮动
 * 
 */
class ClearFloat extends ToolsBase
{
    /**
     * 兼容版本1, 支持iE6,7
     * 
     * - 使用方法, 给父元素应用该class
     * @param string $className class样式名
     * @return 返回一个完整的class样式
     */
    public function v1($className = '.ClearFloat')
    {
        return $this->createClass($className . ':after', 
            $this->css()->css2()->content('.'),
            $this->css()->layout()->display('block '),
            $this->css()->layout()->height('0'),
            $this->css()->layout()->clear('both'),
            $this->css()->layout()->overflow('hidden'),
            $this->css()->layout()->visibility('hidden'),
            $this->css()->onlyIE()->zoom(1, '*'));
        // zoom 缩放,触发 IE下 haslayout，使元素根据自身内容计算宽高。
    }
    
    /**
     * 清除浮动
     * 
     * - 使用方法, 给父元素应用该class
     * @param string $className 样式名, 默认`.ClearFloat`
     */
    public function v3($className = '.ClearFloat')
    {
        // 在元素前后创建伪类隔离
        $css = $this->createClass("{$className}:after,{$className}:before",
            $this->css()->layout()->display('table'),
            $this->css()->css2()->content(""));
        // 清除元素后面创建伪类的左右浮动
        $css .= $this->createClass("{$className}:after",$this->css()->layout()->clear('both'));
        return $css;
    }
    
}