<?php
namespace qpf\provider;

use qpf\base\ServiceProvider;
use qpf\base\Application;
use qpf\lang\Translator;
use qpf\lang\Lang;
use qpf\lang\ArrayLangPack;

/**
 * 语言服务提供商
 */
class LangProvider extends ServiceProvider
{
    /**
     * 注册
     */
    public function register()
    {
        $this->app->single('lang', function(Application $app, $params, $option) {
            $lang = new Lang(new Translator([
                'qpf'  => new ArrayLangPack($app->getQpfPath() . '/langs'), // 框架语言包
                'app'  => new ArrayLangPack($app->getConfigPath() . '/langs'),// 应用程序语言包
            ]));
            
            $lang->auto();
            
            return $lang;
        });
    }

    
}