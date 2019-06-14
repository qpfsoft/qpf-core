<?php
namespace qpf\htmc;

use qpf;

/**
 * Div 代表一个HTML的div元素对象
 *
 *
 * $div = new Div();
 * $div->id = 't1';
 * $div->class = 'went';
 * $div->style = 'widht: 100px;';
 * $div->content = 'Hi, QPF!';
 * echo $div; 打印对象结果， 获取代码直接调用$this->getHtml();
 * // <div id="t1" class="went" style="widht: 100px;">Hi, boy!</div>
 *
 * ~~~
 * div操作对象版本, 需要中途手动接收某个div子对象
 * $h5->body[] = $h5->div()->setContent('1')->setClass('active')->getHtml();
 * $h5->body[] = $h5->div()->setContent('2')->getHtml();
 * $h5->body[] = $h5->div()->setContent('3')->getHtml();
 *
 * // div4嵌套5和6
 * $div4 =$h5->div()->setContent('4');
 * $div4->div()->setContent('5');
 * $div4->div()->setContent('6');
 * $h5->body[] = $div4->getHtml();
 *
 * // div7 > div8 > div9 > div10 连续嵌套
 * $div7 = $h5->div()->setContent('7');
 * $div7->div()->setContent('8')->div()->setContent('9')->div()->setContent('10');
 * $h5->body[] = $div7->getHtml();
 * ~~~
 * 
 * @author qiun
 */
class Div
{

    /**
     * ID名称
     *
     * @var string
     */
    public $id;

    /**
     * 类名
     *
     * @var string
     */
    public $class;

    /**
     * 局部样式
     *
     * @var string
     */
    public $style;

    /**
     * 元素的堆叠顺序 - 高低
     *
     * @var intger
     */
    public $zIndex = 'auto';

    /**
     * 元素缩进等级
     *
     * @var intger
     */
    public $xIndex = 0;

    /**
     * 内容
     *
     * @var string
     */
    public $content = [];

    /**
     * 父元素ID
     *
     * @var string
     */
    public $fid = null;

    /**
     * 构造函数
     *
     * @param array $config 属性配置
     */
    public function __construct($config = [])
    {
        foreach ($config as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * 设置DIV元素ID
     *
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }

    /**
     * 获取DIV元素ID
     *
     * @return string ID名称
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 解析DIV元素ID，用于设置属性
     *
     * @return string
     */
    public function parseId()
    {
        return $this->getId() ? ' id="' . $this->getId() . '"' : '';
    }

    /**
     * 设置元素Class样式名称
     *
     * @param string $class 样式名
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * 获取元素Class样式名称
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * 解析class样式名，用于设置属性
     *
     * @return string
     */
    public function parseClass()
    {
        return $this->getClass() ? ' class="' . $this->getClass() . '"' : '';
    }

    /**
     * 设置DIV元素行样式
     *
     * @param string $style css样式字符串
     * @return \qpf\htmc\Div
     */
    public function setStyle($style)
    {
        $this->style = $style;
        return $this;
    }

    /**
     * 获取元素内联样式
     *
     * @return string css样式
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * 解析元素style属性，用于正确的设置属性
     *
     * @return string
     */
    public function parseStyle()
    {
        return $this->getStyle() ? ' style="' . $this->getStyle() . '"' : '';
    }

    /**
     * 写入一行代码到div内
     *
     * @param string|callable $content 一行代码
     * @return $this
     */
    public function setContent($content)
    {
        if($content instanceof \Closure) {
            $this->content[] = $content();
        } else {
            $this->content[] = $content;
        }
        
        return $this;
    }

    /**
     * 设置div的内容
     *
     * 设置的内容为一次性导入，会覆盖历史内容
     *
     * @param array|string $contents 内容列表或html
     */
    public function setContents($contents)
    {
        if (is_string($contents)) {
            $this->content[] = [];
            $this->content[] = $contents;
        } else {
            $this->content = $contents;
        }
        
        return $this;
    }

    /**
     * 获取当前元素内容所以内容列表
     *
     * @return array 当前元素内的内容列表
     */
    public function getContents()
    {
        return $this->content;
    }

    /**
     * 解析元素内容，确保正确的添加内容
     *
     * @return string 返回元素内容
     */
    public function parseContent()
    {
        $html = '';
        // 统计内容条数
        $count = count($this->content);
        
        // $i 数组自动序列
        // $c 文本或 div对象
        foreach ($this->content as $i => $c) {
            
            if (is_string($c)) {
                // 内容条数大于1，使用当前子元素的缩进格式
                $html .= $count > 1 ? $this->parseXIndex(1) : '';
                $html .= $c;
                // 内容有多条，文本结尾进行换行
                $html .= $count > 1 ? PHP_EOL : '';
            } elseif (is_object($c) && $c instanceof \qpf\htmc\Div) {
                // 内容只有1个，并且是div元素，插入换行
                $html .= $count == 1 ? ($i === 0 ? PHP_EOL : '') : '';
                // 子元素开头缩进
                $html .= $c->parseXIndex();
                $html .= $c->getHtml($c->parseXIndex());
                $html .= PHP_EOL;
            }
        }
        
        return $html;
    }

    /**
     * 创建div元素到内部
     *
     * @param string $id 元素ID
     * @param string $class 元素class样式名称
     * @param string $style 元素行内部css样式
     * @return Div 返回当前创建的元素对象
     */
    public function div($id = '', $class = '', $style = '')
    {
        // 元素站位
        return $this->content[] = new Div([
            'id' => $id,
            'class' => $class,
            'style' => $style,
            // 'zIndex' => ++$zIndex,
            'xIndex' => $this->xIndex + 1,
            'fid' => $this->id !== '' ? $this->id : null
        ]);
    }

    /**
     * 获取当前对象HTML描述字符串
     *
     * @param string $parseXIndex 子元素传递的缩进格式
     * @return string 返回代表当前对象的html代码
     */
    public function getHtml($parseXIndex = null)
    {
        // 统计内部内容数量
        $count = count($this->content);
        
        $html = '';
        $html .= '<div' . $this->parseId() . $this->parseClass() . $this->parseStyle() . '>';
        // 多条内容插入换行
        $html .= $count > 1 ? PHP_EOL : '';
        $content = $this->parseContent();
        $html .= $content;
        
        // 子元素缩进
        if ($this->fid !== null && strpos($content, '</') !== false) {
            $html .= $parseXIndex;
        }
        
        $content = null;
        $html .= '</div>';
        
        return $html;
    }

    /**
     * 返回解析出正确的缩进格式
     *
     * @param integer $n 增加缩进值
     *        1 - 可用于计算出当前级别内容缩进格式，适用于单个元素或文本缩进计算
     * @return string
     */
    public function parseXIndex($n = 0)
    {
        $x = $this->xIndex;
        if ($x == 0)
            return "\t";
        return str_repeat("\t", $x + $n);
    }

    /**
     * 支持对象打印
     *
     * @return string
     */
    public function __toString()
    {
        // return htmlentities($this->getHtml());
        return $this->getHtml();
    }

    public function __destruct()
    {
        $this->id = null;
        $this->style = null;
        $this->class = null;
        $this->zIndex = null;
        $this->content = null;
    }
}