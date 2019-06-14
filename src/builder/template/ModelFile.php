<?php
namespace qpf\builder\template;

/**
 * 模型内容模板
 */
class ModelFile extends PhpFile
{
    /**
     * 类名
     * @var string
     */
    public $className;
    
    
    public function getContent()
    {
        $hello = $this->hello ?: $this->helloWord();
        
        $this->content = $this->buildControllerCode($this->className, $hello);
        
        return parent::getContent();
    }
    
    /**
     * 生成控制器代码
     * @param string $class 控制器类名
     * @param string $hello 默认操作欢迎信息
     * @return string
     */
    public function buildControllerCode($class, $hello)
    {
        return <<<TPL
class $class extends Model
{
   
}
TPL;
    }

}