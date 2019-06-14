<?php
namespace qpf\dispatch\action;

use qpf;
use \qpf\response\Response;
use qpf\error\HttpException;

/**
 * 视图动作
 * 
 * - 显示指定静态页面
 */
class ViewAction extends ActionBase
{
    /**
     * 执行
     */
    public function run(): Response
    {
        $file = QPF::apaths()->getAlias($this->action);
        
        
        if (is_file($file)) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            switch ($ext) {
                case 'php':
                    echo $this->render($file);
                    break;
                case 'html':
                case 'htm':
                    echo file_get_contents($file);
                    break;
            }
        } else {
            throw new HttpException(404, 'View Not Found');
        }
        
        exit;
    }
    
   /**
    * 渲染参数模板
    *
    * 模板参数操作格式`$var`
    *
    * @param array $params 关联数组, 模板变量值
    * @param string $tpl 模板文件, 支持路径别名
    * @return string 返回渲染结果
    */
    protected function render($file)
    {
        ob_start();
        ob_implicit_flush(false);
        include ($file);
        return ob_get_clean();
    }
}