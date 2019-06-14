<?php
namespace qpf\htmc\html;


use qpf\exception\QPFException;

/**
 * DIV 标签
 * 
 * 结束对象的方法[[getHtml()]]或开始标签[[tagStart()]]结束标签[[tagEnd()]].
 * 
 * ~~~
        // 最新版本的div标签对象.
        $h5->body[] = $h5->html()->div()->add('1')->classAttr('active')->getHtml();
        $h5->body[] = $h5->html()->div()->add('2')->getHtml();
        $h5->body[] = $h5->html()->div()->add('3')->getHtml();
        
        // div4 中保护 div5和div6 ,  可以将div4保存到一个变量来操作.
        $h5->html()->div()->content('4');
        $h5->html()->div()->addDiv()->content('5');
        $h5->html()->div()->addDiv()->content('6');
        $h5->body[] = $h5->html()->div()->getHtml();
        
        // 效果同上.
        //$div4 = $h5->html()->div()->content('4');
        //$div4->addDiv()->content('5');
        //$div4->addDiv()->content('6');
        //$h5->body[] = $div4->getHtml();
        
        // div7 > div8 > div9 > div10
        $h5->html()->div()->content('7')->addDiv()->content('8')->addDiv()->content('9')->addDiv()->content('10');
        $h5->body[] = $h5->html()->div()->getHtml();
 * ~~~
 * 
 * 代码美化规则:
 * - 根据fid级别, 从0开始表示顶级父标签. 不缩进. 数字每增加1. 标签前缀fid数量的`\t`制表符号.
 * - 如果元素内容为文本 开始和结束标签不换行. 包含就开始和结束标签进行换行包裹.
 * 
 * 
 * 该对象方法:
 * - [[content()]] : 设置元素内容.
 * - [[add()]] : 添加一行内容到元素, 即追加内容到元素内容尾部. 
 * - [[addDiv()]] :　添加一个子div元素. 无需手动结束对象. 即不用执行[[getHtml()]]来方法html代码.
 * - [[tagStart()]] : 标签开始
 * - [[tagEnd]()] : 标签结束
 * - [[getHtml()]] : 返回标签对象的html代码.
 * 
 * @author qiun
 *
 */
class Div extends HtmlBase
{
    /**
     * 标签名称
     * @var string
     */
    protected $tagName = 'div';
    
    /**
     * 子元素列表
     * @var array
     */
    public $content = [];
    
    /**
     * 父ID 用来标识div嵌套级别
     * @var integer
     */
    protected $fid = 0;
    
    /**
     * 写入一行内容到元素内
     * 
     * - [[content]]将直接写入并覆盖元素内容.
     * - [[add]]和[[content]]方法不能同时使用.
     * - 同时使用时[[content]]方法写入内容在前.
     * 
     * @param string|array $content
     * @return $this
     */
    public function add($content)
    {
        $this->content[] = $content;
        return $this;
    }
    
    /**
     * 解析元素内容行
     */
    protected function parseContent()
    {
        // 未添加反行内容
        if (empty($this->content)) return;
        if (!is_array($this->content)) {
        }
        
        // 结束子DIV元素
        foreach ($this->content as $i => $val) {
            if ($val instanceof $this) { // div
                $this->content[$i] = $val->getHtml();
            } elseif ($val instanceof \qpf\htmc\html\HtmlBase) { // html
                //$this->content[$i] = $val->getHtml();
                QPFException::user('Htmc的html标签对象`' . get_class($val) . '`, 需要正确的关闭并返回html代码. 确保能正确的复用对象.');
            }
        }

       /* 元素内容不为空时, add的行内容将追加在末尾.
        * add()添加的内容开始标签后面会换行
        * content()添加的内容不会换行
        * 规则: 元素内容是纯文本. 不应该换行. 
        */
        
        // 没有设置元素内容, 将行内容转换为字符串. 赋值给元素内容.
        if (is_null($this->tagContent)) {
            // 如果add()添加的行内容包含标签. 在内容前换行.
            $content = implode('', $this->content);
            if (strpos($content, '</')) {
                $content = PHP_EOL . $content;
            }
            $this->tagContent = $content;
        
        // 如果设置了元素内容, 将行内容追加到元素内容后面, 行内容前换行.
        } elseif (!empty($this->content)) {
            $this->tagContent .= PHP_EOL . implode('', $this->content);
        }
    }
    
    /**
     * 添加一个子DIV元素.
     * 
     * - 子元素不能使用[[tagStart()]]和[[tagEnd()]]2个方法.
     * @return $this 返回一个新div对象
     */
    public function addDiv()
    {
        $div = new self();
        // 自身是顶级元素, 将子元素赋值为1.
        if ($this->fid == 0) {
            $div->fid = 1;
        } else {
            // 自身也是子元素. 进行累加父级别
            $div->fid = $this->fid + 1;
        }
        
        $this->content[] = $div;
        
        return $div;
    }
    
    /**
     * 标签开始
     */
    public function tagStart()
    {
        $this->parseContent();
        $code = "<{$this->tagName}{$this->parseAttr()}>";
        return $code;
    }
    
    /**
     * nt(
     * 
     * - 闭合时将填充内容, 并重置对象
     * @return string
     */
    public function tagEnd()
    {
        $code = $this->tagContent. '</'. $this->tagName .'>';
        $this->reset();
        return $code;
    }
    
    /**
     * 获得HTML代码
     * @see \qpf\htmc\html\HtmlBase::getHtml()
     */
    public function getHtml()
    {
        // 整合行内容
        $this->parseContent();
        
        $code = '';
        
        // 子元素开始标签前, 添加自身级别的缩进
        if ($this->fid > 0) {
            $code.= str_repeat("\t", $this->fid);
        }

        // 开始标签
        $code.= "<{$this->tagName}{$this->parseAttr()}>{$this->tagContent}";
        
        // 结束标签
        $code.= "</{$this->tagName}>";
        
        // 子元素结束标签后, 为父元素结束符, 前缀换行+缩进. 
        // (为下一行文本设置的换行和缩进)
        if ($this->fid > 0) {
            $code.= PHP_EOL . str_repeat("\t", $this->fid - 1); // 这里-1代表父元素的缩进级别.
        }
        
        // 重置对象
        $this->reset();
        return $code;
    }
    
    /**
     * 重置对象
     * 
     * @see \qpf\htmc\html\HtmlBase::reset();
     */
    public function reset()
    {
        parent::reset();
        $this->content = [];
        $this->fid = 0;
    }
}