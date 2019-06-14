<?php
namespace qpf\builder\template;

use qpf\base\Core;
use qpf\exceptions\ParameterException;
use qpf\file\Dir;

/**
 * 文件模板抽象类
 */
abstract class FileTemplate extends Core implements BuildTemplateInterface
{
    /**
     * 注解信息
     * @var array
     */
    public $comment = [];
    /**
     * 文件名
     * @var string
     */
    public $name;
    /**
     * 文件扩展名 - 不含点
     * @var string
     */
    public $ext;
    /**
     * 文件内容
     * @var string
     */
    public $content;

    /**
     * 换行符
     * @return string
     */
    public static function eol()
    {
        return PHP_EOL;
    }
    
    /**
     * 设置文件内容
     * @param string $content
     */
    public function setContent(string $content)
    {
        $this->content = $content;
    }
    
    /**
     * 获取文件内容
     * @return string
     */
    abstract public function getContent();

    /**
     * 获取文件名
     * @return string
     */
    public function getFileName()
    {
        return $this->name . '.' . $this->ext;
    }
    
    
    /**
     * 设置注解段落
     * @param array $comments 注解数组, 每个元素代表一行
     */
    public function setComments(array $comments)
    {
        $this->comment = $comments;
    }
    
    /**
     * 获取注解
     * @return array
     */
    public function getComments()
    {
        return $this->comment;
    }
    
    /**
     * 添加一行注解
     * @param string $row 行内容
     */
    public function addRowComment($row)
    {
        $this->comment[] = $row;
    }
    
    /**
     * 保存文件
     * @param string $file 文件路径
     * @return number
     */
    public function save($file)
    {
        if (empty($file)) {
            throw new ParameterException('No save location specified');
        } else {
            // 自动创建目录, 
            $dir = dirname($file);
            Dir::single()->createDir($dir);
            // 检查写入权限
            if (!is_writable($dir)) {
                return false;
            }
        }
        
        return file_put_contents($file, $this->getContent());
    }
    
    /**
     * 渲染模板
     * @param string $tpl 模板内容, 标签格式`{:name}`
     * @param array $args 替换参数, 键值数组.['nane' => 'value']
     * @return string
     */
    public function render(string $tpl, array $args)
    {
        $search = [];
        foreach ($args as $name => $value) {
            $search[] = "{:$name}";
        }
        
        return str_replace($search, $args, $tpl);
    }
}