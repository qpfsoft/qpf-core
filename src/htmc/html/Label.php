<?php
namespace qpf\htmc\html;

/**
 * Label标签为鼠标用户改进了可用性。
 *
 * 如果您在 label 元素内点击文本，就会触发此控件。
 * label增加了触摸的区域, label内容一般是文字描述 , 通过绑定一个元素的id即可, 点击label的内容可以让该元素获得焦点.
 * label元素不一定要包裹绑定的input元素. 只需要绑定id即可.
 *
 * 使用实例:
 * ```
 * // 使用tag()方法快捷创建label标签
 * $hc->body[] = $hc->form()->label()->tag('1万两等于 = ' . $hc->input()->text('mbit') . ' 梦幻币<br>' , 'mbit');
 * ```
 *
 *
 * "for" 属性可把 label 绑定到另外一个元素。
 * 请把 "for" 属性的值设置为相关元素的 id 属性的值。
 *
 * 显式的联系：<label for="SSN">Social Security Number:</label>
 * 隐式的联系：<label>Date of Birth: <input type="text" name="DofB" /></label>
 * 
 * @author qiun
 *        
 */
class Label extends TagAttr
{

    /**
     * 触摸区域绑定的元素id
     * 
     * @param string $id            
     * @return $this
     */
    public function forID($id)
    {
        $this->attr(['for' => $id]);
        return $this;
    }

    /**
     * 生成开始标签
     *
     * @param string $option 标签内容, 一般为input描述文本
     * @param string|array $id 标签属性
     * @return string
     */
    public function tagStart($content = null, $attr = null)
    {
        $this->attr($attr);
        return '<label' . $this->parseAttr() . '>' . $content;
    }

    /**
     * 生成结束标签
     * 
     * @return string
     */
    public function tagEnd()
    {
        return '</label>';
    }

    /**
     * 快捷创建label标签
     *
     * 实例:
     * ```
     * $option = '用户名:< input id="user" >'; 
     * $this->tagBuild('user', $option);
     * ```
     *
     * @param string $content 标签内容
     * @param string|array $id 标签属性, 可能的值:
     * - string : 直接设置for绑定的元素ID
     * - array : 标签属性设置数组
     * @return string
     */
    public function tagBuild($content = null, $attr = null)
    {
        if (!empty($attr) && is_string($attr)) {
            $this->forID($attr);
            $attr = null;
        }
        return $this->tagStart($content, $attr) . $this->tagEnd();
    }
}