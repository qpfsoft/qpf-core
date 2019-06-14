<?php
namespace qpf\builder\template;

/**
 * 生成PHP文件
 */
class PhpFile extends FileTemplate
{
    /**
     * 文件命名空间
     * @var string
     */
    public $namespace;
    
    /**
     * 声明页面类命名空间
     * @var array
     */
    public $use = [];
    
    /**
     * 获取文件内容
     * @return string
     */
    public function getContent()
    {
        $code = $this->buildPHPCode();
        
        return $this->quotePHPCode($code);
    }
    
    /**
     * 生成PHP代码
     * @return string
     */
    public function buildPHPCode()
    {
        // 命名空间
        $namespace = $this->buildNameSpan($this->namespace);
        
        // 类声明
        $use = $this->buildUse($this->use);
        
        // 注解
        $comment = $this->buildPHPComment($this->comment);
        
        return $namespace . $use . $comment . $this->content;
    }
    
    /**
     * 生成命名空间代码段
     * @param string $namespace 命名空间
     * @return string
     */
    public function buildNameSpan($namespace)
    {
        return empty($namespace) ? '' : 'namespace ' . $namespace . ';' . self::eol() . self::eol();
    }
    
    /**
     * 生成类空间声明
     * @param array $use
     * @return string
     */
    public function buildUse(array $use)
    {
        if (empty($use)) {
            return '';
        }
        
        $result = [];
        
        foreach ($use as $i => $value) {
            $result[] = 'use ' . $value . ';';
        }
        
        return join(self::eol(), $result) . self::eol() . self::eol();
    }
    
    
    /**
     * 引用PHP代码
     * @param string $code PHP代码
     * @param bool $end 是否结束标签包裹, 默认`false`.
     * @return string
     */
    public function quotePHPCode($code, $end = false)
    {
        return '<?php' . self::eol() . $code . ($end ? self::eol() . '?>' : '');
    }
    
    /**
     * 生成PHP多行注解
     * @param array $comment 注解行信息
     * @return string
     */
    public function buildPHPComment(array $comment)
    {
        if (empty($comment)) {
            return '';
        }
        
        $str = '/**' . self::eol();
        
        foreach ($comment as $txt) {
            $str .= ' * '. $txt . self::eol();
        }
        $str .= ' */' . self::eol();
        
        return $str;
    }
}