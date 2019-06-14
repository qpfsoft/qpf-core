<?php
namespace qpf\console\command\fast;

use qpf\builder\template\PhpFile;
use qpf\builder\code\ArrayCode;

/**
 * 生成加速
 * 
 * - 预先将加载注册的文件合并到一个文件
 */
class Loader
{
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
    }
    
    public function getfileName()
    {
        return $this->path . '/FastLoad.php';
    }
    
    public function getMap()
    {
        return include $this->path . '/map.php';
    }
    
    public function getNamespace()
    {
        return include $this->path . '/namespace.php';
    }
    
    public function getDir()
    {
        return include $this->path . '/dir.php';
    }
    
    public function build()
    {
        $tpl = new PhpFile();
        $tpl->name = 'FastLoad';
        $tpl->ext = 'php';
        $tpl->setComments([
            '加载加速文件',
            '',
            '该文件由系统生成, 重新生成命令`qpf php fast:loader`'
        ]);
        
        $tpl->namespace = 'qpf\\autoload';
        
        
        $content = $tpl->render($this->getContent(), [
            'map'   => ArrayCode::build($this->getMap()),
            'namespace' => ArrayCode::build($this->getNamespace()),
            'dir' => ArrayCode::build($this->getDir()),
        ]);

        $tpl->setContent($content);
        
        if ($tpl->save($this->getfileName())) {
            return $this->getfileName();
        }
        
        return false;
    }

    
    protected function getContent():string
    {
        return $text = <<<tpl
class FastLoad
{
    public static \$map = {:map};
    public static \$namespace = {:namespace};
    public static \$dir = {:dir};
    
    public static function register()
    {
        Autoload::setClassMap(self::\$map);
        Autoload::setNamespace(self::\$namespace);
        Autoload::setLoadDir(self::\$dir);
        
        Autoload::register();
    }
}
tpl;
    }
}