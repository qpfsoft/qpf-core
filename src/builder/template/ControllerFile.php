<?php
namespace qpf\builder\template;

/**
 * 控制器内容模板
 */
class ControllerFile extends PhpFile
{
    /**
     * 控制器类名
     * @var string
     */
    public $className;
    /**
     * 空控制器的欢迎信息
     * @var string
     */
    public $hello;
    
    
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
class $class extends Controller
{
            
    public function actionIndex()
    {
        echo '$hello';
    }
            
}
TPL;
    }
    
    
    
    /**
     * 返回模板
     * @return string
     */
    public function getTpl()
    {
        return <<<TPL
<?php
namespace {:namespace}\controller;
            
use qpf;
use qpf\base\Controller;
            
/**
 * {:controllerName}
 */
class {:controllerName} extends Controller
{
            
    public function actionIndex()
    {
        echo '{:hello}';
    }
            
}
TPL;
    }
    
    /**
     * 欢迎视图
     * @return string
     */
    protected function helloWord()
    {
        return '<!doctype html><html><head><meta charset="utf-8"><title>欢迎使用QPF!</title><style type="text/css">body{margin-left:20px;background-color:#FFF}.qpf-h1,.qpf-h2{font-family:微软雅黑}.qpf-h1{color:#24BD05;margin-bottom:30px;margin-top:30px;font-size:50px}.qpf-h2{color:#636363}.logo-main{font-size:18px;font-family:Arial;font-style:oblique;color:#FFF;background-color:#555454;width:25px;height:25px;padding-top:5px;padding-right:5px;padding-left:5px;padding-bottom:5px;margin-top:-55px;margin-left:99px;position:absolute}.logo-main-q{float:left;position:absolute}.logo-main-p{position:absolute;margin-left:10px}.logo-main-f{position:absolute;margin-top:6px;margin-left:9px;float:left}.qpf-hr{width:400px;border-bottom:1px dashed #D7D7D7}.qpf-hi-main{overflow:hidden;}</style></head><body><div class="qpf-hi-main"><h1 class="qpf-h1">:) 运行成功！</h1><div class="qpf-hr">&nbsp;</div><h2 class="qpf-h2">欢迎使用&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;，该页面由系统自动生成！</h2><div class="logo-main"><div class="logo-main-q">Q</div><div class="logo-main-p">P</div><div class="logo-main-f">F</div></div></div></body></html>';
    }
}