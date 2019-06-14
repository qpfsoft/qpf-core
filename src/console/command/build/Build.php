<?php
namespace qpf\console\command\build;

use qpf;
use qpf\console\command\Cmd;
use qpf\builder\AppDirstruc;
use qpf\builder\template\ResourceControllerFile;
use qpf\file\Dir;

class Build extends Cmd
{
    
    /**
     * 默认执行操作
     */
    public function run()
    {
        // 打印可用操作
        return $this->help();
    }
    
    /**
     * 创建应用模块
     * 
     * @cmd `qpf build:app index`
     * 
     * @param string $app 应用模块名
     */
    public function app($name)
    {
        if (!preg_match('/^\w+$/', $name)) {
            return false;
        }
        
        // 规范, 目录名统一小写
        $name = strtolower($name);
        
        if (QPF::app()->isDebug() && QPF::app()->isDev()) {
            $zonePath = QPF::$app->getZonePath();
            if (!is_dir($zonePath .'/'. $name . '/controller/')) {
                $build = new AppDirstruc();
                $config = $build->buildModuleSetup($name, $zonePath);
                $build->setup($config);
                return $build->log();
            } else {
                return 'app existed!';
            }
        }
    }
    
    /**
     * 创建控制器
     * 
     * - 不会覆盖已存在的控制器
     * @param string $path `模块名/控制器`
     */
    public function controller($path)
    {
        if (!preg_match('/^\w+\/\w+$/', $path)) {
            return false;
        }
        
        list($app, $file) = explode('/', $path);
        $path = $this->app->getZonePath() . '/' . $app . '/controller';
        
        // 模块不存在, 将不可直接创建控制器
        if (!is_dir($path)) {
            return "app `{$app}` does not exist!";
        }
        
        $build = new AppDirstruc();
        $config = [
            $build::IController => [$file],
        ];
        $build->setup($config, $path);
        return $build->log();
    }
    
    /**
     * 创建资源控制器
     * 
     * - 不会覆盖已存在的控制器
     * @param string $path `模块名/控制器`
     */
    public function resource($path)
    {
        if (!preg_match('/^\w+\/\w+$/', $path)) {
            return false;
        }
        
        list($app, $name) = explode('/', $path);
        $path = $this->app->getZonePath() . '/' . $app . '/controller';
        
        // 模块不存在, 将不可直接创建控制器
        if (!is_dir($path)) {
            return "app `{$app}` does not exist!";
        }
        
        $build = new ResourceControllerFile();
        $build->className = ucfirst($name) . 'Controller';
        $build->namespace = $this->app->getZonename() . '\\' . $app . '\\controller';
        $build->use = [
            'qpf\web\Controller',
        ];
        $build->comment = [
            $name . ' 资源控制器',
            '',
            '该控制器由系统自动生成!',
        ];
        
        if (is_file($path . '/' . $build->className . '.php')) {
            return $build->namespace .'\\'. $build->className . ' existed!';
        }
        
        return Dir::single()->createFile($path . '/' . $build->className . '.php', $build->getContent()) > 0;
    }
    
    public function init($opt, $param, $end = '')
    {
        if(strpos($opt, '--') == 0) {
            return 'is option : ' . $opt;
        }
        return print_r(func_get_args(), true);
    }
    
    
}