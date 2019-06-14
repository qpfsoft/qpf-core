<?php
namespace qpf\builder\template;

/**
 * 资源控制器内容模板
 */
class ResourceControllerFile extends ControllerFile
{
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
    /**
     * index 首页, 资源列表展示
     * @method any
     */
    public function actionIndex()
    {
        echo '$hello';
    }
    
    /**
     * create 创建资源, 表单页面
     * @method get
     */
    public function actionCreate()
    {
        
    }
    
    /**
     * save 保存资源, 表单提交页面
     * @method post
     */
    public function actionSave()
    {
        
    }

    /**
     * read 读取资源
     * @method get
     * @param int \$id 资源id
     */
    public function actionRead(\$id)
    {
        
    }

    /**
     * edit 编辑资源, 表单内容填充页
     * @method get
     * @param int \$id 资源id
     */
    public function actionEdit(\$id)
    {
        
    }

    /**
     * update 更新资源
     * @method put
     * @param int \$id 资源id
     */
    public function actionUpdate(\$id)
    {
        
    }

    /**
     * delete 删除资源
     * @method delete
     * @param int \$id 资源id
     */
    public function actionDelete(\$id)
    {
        
    }
}
TPL;
    }
}