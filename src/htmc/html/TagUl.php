<?php
namespace qpf\htmc\html;

/**
 * ul 标签
 * 
 * 帮助用户创建ul > li
 * @author qiun
 *
 */
class TagUl
{
    /**
     * ul标签对象
     * @var Tag
     */
    protected $ul;
    /**
     * li标签对象
     * @var Tag
     */
    protected $li;
    /**
     * 当前ul对象的li配置列表
     * @var Tag[]
     */
    private $_li = [];
    
    /**
     * 对象静态创建
     * @return \qpf\htmc\html\TagUl
     */
    public static function this()
    {
        return new static;
    }
    
    /**
     * 初始化ul标签对象
     */
    protected function init()
    {
        if ($this->ul === null) {
            $this->ul = new Tag('ul');
        }
    }
    
    /**
     * 创建ul标签
     * @param string|array $ul_attr ul标签的属性
     * @return \qpf\htmc\html\TagUl
     */
    public function ul($ul_attr = null)
    {
        $this->init();
        
        if (!is_null($ul_attr)) {
            $this->ul->attr($ul_attr);
        }
        return $this;
    }
    
    /**
     * 在ul中创建li标签
     * @param string $num 数量
     * @return \qpf\htmc\html\TagUl
     */
    public function li($num)
    {
        for ($i = 1; $i <= $num; $i++) {
            $this->_li[$i] =  new Tag('li');
        }
        return $this;
    }
    
    /**
     * 设置指定li的属性
     * @param string $id 编号id, 从1开始
     * @param string $attr li标签属性, 无需求设置为`null`
     * @param string $content li标签的内容
     * @return \qpf\htmc\html\TagUl
     */
    public function liAttr($id, $attr, $content)
    {
        $this->_li[$id]->attr($attr)->content($content);
        return $this;
    }
    
    /**
     * 指定所有li的属性
     * 
     * - 需要注意和[liAttr]方法一起使用时,先后顺序造成的覆盖问题
     * @param string $attr li标签属性, 无需求设置为`null`
     * @param string $content li标签的内容, 定制内容标签:
     * - `{li-id}` : 文本中的该标签会被替换为当前li的id序列号
     * @return \qpf\htmc\html\TagUl
     */
    public function liAttrAll($attr, $content)
    {
        foreach ($this->_li as $id => $objLi) {
            
            $this->_li[$id]->attr($attr)->content(str_replace('{li-id}', $id, $content));
        }
        return $this;
    }
    
    /**
     * 解析ul中的li对象
     * @return string
     */
    protected function parseLi()
    {
        $code = '';
        foreach ($this->_li as $i => $objLi) {
            $code .= $objLi->getHtml() . PHP_EOL;
        }
        return $code;
    }
    
    /**
     * 获得html代码
     * 
     * @param boolean 是否只返回li标签,并不需要ul标签包裹, 
     * - 默认true,会被ul标签包裹
     * @return string
     */
    public function getHtml($ulTag = true)
    {
        $html = '<!-- Start: build by htmc/html/TagUl -->' . PHP_EOL;
        if ($ulTag && $this->ul !== null) {
            $html .= $this->ul->content(PHP_EOL . $this->parseLi())->getHtml() . PHP_EOL;
        } else {
            $html .= $this->parseLi();
        }
        $html .= '<!-- End: build by htmc/html/TagUl -->' . PHP_EOL;
        $this->reset();
        return $html;
    }
    
    /**
     * 重设
     */
    protected function reset()
    {
        $this->_li = [];
        $this->ul = null;
        $this->li = null;
    }
}