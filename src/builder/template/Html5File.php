<?php
namespace qpf\builder\template;

/**
 * HTML5 文件模板
 */
class Html5File extends FileTemplate
{
    /**
     * 页面加载样式列表
     * @var array
     */
    public $css = [];
    /**
     * 页面加载脚本列表
     * @var array
     */
    public $js = [];
    
    /**
     * 获取模版内容
     */
    public function getContent(): string
    {
        $this->content = $this->render($this->getTpl(), [
            'css' => $this->parseCss($this->css),
            'js'  => $this->parseJs($this->js),
        ]);
        
        return $this->content;
    }
    
    public function addCss($href): void
    {
        $this->css[] = $href;
    }
    
    public function addJs($src): void
    {
        $this->js[] = $src;
    }
    
    public function importCss(array $hrefs): void
    {
        $this->css = array_merge($this->css, $hrefs);
    }
    
    public function importJs(array $srcs): void
    {
        $this->js = array_merge($this->js, $srcs);
    }
    
    
    protected function parseCss($css): string
    {
        if (is_array($css)) {
            $list = [];
            foreach ($css as $i => $href) {
                $list[] = $this->parseCss($href);
            }
            return join(PHP_EOL, $list);
        } else {
            return '<link rel="stylesheet" href="' . $css . '">';
        }
    }
    
    protected function parseJs($js): string
    {
        if (is_array($js)) {
            $list = [];
            foreach ($js as $i => $src) {
                $list[] = $this->parseJs($src);
            }
            return join(PHP_EOL, $list);
        } else {
            return '<script src="' . $js . '"></script>';
        }
    }
    
    protected function getTpl(): string
    {
        return <<<TPL
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge,chrome=1">
    <title>Document</title>
    {:css}
</head>
<body>

    {:js}
</body>
</html>
TPL;
    }
}