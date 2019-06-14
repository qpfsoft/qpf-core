<?php
namespace qpf\base;

use qpf\exceptions\ParameterException;

/**
 * 别名路径 (@path)
 */
class Apaths
{
    /**
     * 别名标识前缀
     * @var string
     */
    public $prefix = '@';
    /**
     * 别名映射列表
     * @var array
     */
    public $alias = [];
    
    /**
     * 路径是否包含别名
     * @param string $path
     * @return bool
     */
    public function isAlias(string $path): bool
    {
        return strncmp($path, $this->prefix, 1) === 0 ? true : false;
    }
    
    /**
     * 设置别名路径
     * @param string $alias 别名, 会自动前缀标识
     * @param string $path 路径
     * @return void
     */
    public function setAlias(string $alias, string $path)
    {
        if (!$this->isAlias($alias)) {
            $alias = $this->prefix . $alias;
        }
        
        list($root) = $this->getRootDir($alias);
        
        if (!empty($path)) {
            $path = strtr($path, '\\', '/');
            $path = $this->isAlias($path) ? $this->getAlias($path) : rtrim($path, '\\/');
            if (!isset($this->alias[$root])) {
                if ($root === $alias) {
                    $this->alias[$root] = $path;
                } else {
                    $this->alias[$root] = [$alias => $path];
                }
            } elseif (is_string($this->alias[$root])) {
                if ($root === $alias) {
                    $this->alias[$root] = $path;
                } else {
                    $this->alias[$root] = [
                        $alias  => $path,
                        $root   => $this->alias[$root],
                    ];
                }
            } else {
                $this->alias[$root][$alias] = $path;
                krsort($this->alias[$root]);
            }
        } else {
            $this->delAlias($alias, $root);
        }
    }
    
    /**
     * 获取别名路径的实际路径
     * @param string $alias 别名路径, 必须手动添加前缀标识
     * @param bool $throw 无效别名是否抛出异常
     * @throws ParameterException
     * @return string|false
     */
    public function getAlias(string $alias, $throw = true)
    {
        if (!$this->isAlias($alias)) {
            return $alias;
        }
        
        $parse = $this->parseAlias($alias);

        if ($parse !== false) {
            return $parse['path'];
        }
        
        if ($throw) {
            throw new ParameterException('Invalid path alias:' . $alias);
        }
        
        return false;
    }
    
    /**
     * 解析路径中的别名
     * @param string $alias
     * @return array 返回匹配的别名项与解析出的实际路径
     */
    public function parseAlias(string $alias)
    {
        list($root, $pos) = $this->getRootDir($alias);
        
        if (isset($this->alias[$root])) {
            if (is_array($this->alias[$root])) {
                foreach ($this->alias[$root] as $name => $path) {
                    if (strpos($alias . '/', $name . '/') === 0) {
                        return ['alias' => $name, 'path' => $path . substr($alias, strlen($name))];
                    }
                }
            } else {
                return [
                    'alias' => $root,
                    'path' => $root === $alias ?  $this->alias[$root] : 
                    $this->alias[$root] . substr($alias, $pos)];
            }
        }
        
        return false;
    }
    
    /**
     * 删除别名路径
     * @param string $alias
     */
    public function delAlias(string $alias, $root = null)
    {
        if ($root === null) {
            list($root) = $this->getRootDir($alias);
        }
        
        if (isset($this->alias[$root])) {
            if (is_array($this->alias[$root])) {
                unset($this->alias[$root][$alias]);
            } elseif ($root === $alias) {
                unset($this->alias[$root]);
            }
        }
    }
    
    /**
     * 获取路径的根目录
     * @param string $path 路径
     * @return array 返回格式`[root, $pos]`
     */
    public function getRootDir(string $path): array
    {
        $pos = strpos($path, '/');
        return [$pos === false ? $path : substr($path, 0, $pos), $pos];
    }
    
    /**
     * 获取路径的别名部分
     * ```
     * // 注册的别名路径`@root/static` => '/root/www/static'
     * '@root/static/image' // 返回`@root/static`部分
     * ```
     * @param string $path 路径
     * @return string
     */
    public function getPathAlias(string $path): string
    {
        if (!$this->isAlias($path)) {
            return false;
        }
        
        $parse = $this->parseAlias($path);
        
        if ($parse !== false) {
            return $parse['alias'];
        }
        
        return $parse;
    }
    
    /**
     * 批量注册别名路径
     * @param array $aliases
     */
    public function setAliases(array $aliases)
    {
        foreach ($aliases as $alias => $path) {
            $this->setAlias($alias, $path);
        }
    }
    
    /**
     * 获取全部别名路径
     * @return array
     */
    public function getAliases()
    {
        return $this->alias;
    }
}