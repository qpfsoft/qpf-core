<?php
namespace qpf\file;

use qpf\base\SingleTrait;

/**
 * 目录操作类
 */
class Dir
{
    use SingleTrait;
    
    /**
     * 返回指定路径下的文件与目录
     * @param string $path 路径
     * @return array
     */
    public function dir($path)
    {
        return $this->li($path);
    }
    
    /**
     * 返回指定路径下文件与目录的详细信息
     * @param string $path 路径
     * @return array
     */
    public function li($path)
    {
        $lists = [];
        
        if(empty($path)) {
            return $lists;
        }
        
        foreach (glob($path . '/*') as $i => $file) {
            $lists[$i]['path'] = $file;
            $lists[$i]['type'] = filetype($file);
            $lists[$i]['size'] = $this->size($file);
            $lists[$i]['iswrite'] = is_writeable($file);
            $lists[$i]['isread'] = is_readable($file);
            $lists[$i]['filemtime'] = filemtime($file); // 文件修改时间
            $lists[$i]['fileatime'] = fileatime($file); // 文件的上次访问时间
            $info = pathinfo($file);
            foreach ($info as $key => $val) {
                $lists[$i][$key] = $val;
            }
            if (!isset($lists[$i]['extension'])) {
                $lists[$i]['extension'] = '';
            }
        }
        
        return $lists;
    }
    
    /**
     * 返回文件或目录下文件的大小
     * @param string $path 文件或目录
     * @return int 单位字节
     */
    public function size($path)
    {
        $size = 0;
        
        if(is_file($path)) {
            $size = filesize($path);
        } elseif(is_dir($path)) {
            foreach (glob($path . '/*') as $name) {
                $size += $this->size($name);
            }
        }
        
        return $size;
    }
    
    /**
     * 删除文件或目录
     * @param string $file 文件路径
     * @return bool
     */
    public function delete($file)
    {
        if(is_dir($file)) {
            return $this->deleteDir($file);
        }
        
        return $this->deleteFile($file);
    }
    
    /**
     * 删除文件
     * @param string $file 文件路径
     * @return bool
     */
    public function deleteFile($file)
    {
        if (is_file($file)) {
            return unlink($file);
        }
        
        return true;
    }
    
    /**
     * 删除目录与目录下的文件
     * @param string $path 目录路径
     * @return bool
     */
    public function deleteDir($path)
    {
        if(!is_dir($path)) {
            return true;
        }
        // 列出指定路径中的文件和目录, 两个数组的差集
        $files = array_diff(scandir($path), ['.', '..']);
        
        foreach ($files as $file) {
            if(is_dir($path . '/' . $file)) {
                $this->deleteDir($path . '/' . $file);
            } else {
                unlink($path . '/' . $file);
            }
        }
        
        return rmdir($path);
    }
    
    /**
     * 创建目录 - 支持多级嵌套
     * @param string $path 目录路径
     * @param int $mode 目录权限
     * @return bool
     */
    public function createDir($path, $mode = 0755)
    {
        if(!empty($path)) {
            return (!is_dir($path) && mkdir($path, $mode, true));
        }
    }
    
    /**
     * 新建文件 - 自动创建目录
     * @param string $file 文件路径
     * @param string $content 写入内容
     * @return bool
     */
    public function createFile($file, $content = '')
    {
        $this->createDir(dirname($file));
        return file_put_contents($file, $content);
    }
    
    /**
     * 检查文件是否存在 - 区分大小写
     * @param string $file 文件路径
     * @return boolean
     */
    public function isFile($file)
    {
        if (is_file($file)) {
            // 仅在windoe平台检查
            if (strstr(PHP_OS, 'WIN')) {
                if (basename(realpath($file)) != basename($file)) {
                    return false;
                }
            }
            return true;
        }
        
        return false;
    }
    
    /**
     * 创建文件时自动创建目录
     * @param string $file 文件路径
     * @param string $mode 目录权限
     * @param string $content 文件内容
     */
    public function create($file, $mode = 0755, $content = '')
    {
        $parts = explode('/', $file);
        $file = array_pop($parts);
        
        $dir = '';
        foreach($parts as $part) {
            if(!is_dir($dir .= "/$part")) {
                mkdir($dir, $mode);
            }
        }
        
        file_put_contents($dir . '/' . $file, $content);
    }
    
    /**
     * 复制目录下内容到新目录
     * @param string $path 原目录路径
     * @param string $new 新目录路径
     * @param int $mode 目录权限
     * @return bool
     */
    public function copyDir($src, $dst, $mode = 0755): bool
    {
        if (is_dir($src)) {
            $handle = opendir($src);
            while (($item = readdir($handle)) !== false) {
                if($item !== '.' && $item !== '..') {
                    if(is_file($src . '/' . $item)) {
                        !is_dir($dst) && mkdir($dst, $mode, true);
                        copy($src . '/' . $item, $dst . '/' . $item);
                    } elseif (is_dir($src . '/' . $item)) {
                        $this->copyDir($src . '/' . $item, $dst . '/' . $item);
                    }
                }
            }
            closedir($handle);
            return true;
        } else {
            return false;
        }
        
    }
    
    /**
     * 复制文件
     * 
     * - 支持自动创建目标目录
     * @param string $file 源文件
     * @param string $new 目标文件
     * @return bool
     */
    public function copyFile(string $file, string $new): bool
    {
        if(!is_file($file)) {
            return false;
        }
        
        $this->createDir(dirname($new));
        
        return copy($file, $new);
    }
    
    /**
     * 移动目录
     * @param string $path 原目录路径
     * @param string $new 目标目录路径
     * @return bool
     */
    public function moveDir($path, $new)
    {
        if($this->copyDir($path, $new)) {
            return $this->deleteDir($path);
        }
    }
    
    /**
     * 移动文件
     * @param string $file 原文件路径
     * @param string $path 移动到的目录路径
     * @param int $mode 目录权限
     * @return bool
     */
    public function moveFile($file, $path, $mode = 0755)
    {
        is_dir($path) && mkdir($path, $mode, true);
        
        if(is_file($file) && is_dir($path)) {
            copy($file, $path . '/' . basename($file));
            
            return unlink($file);
        }
    }
}