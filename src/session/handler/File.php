<?php
namespace qpf\session\handler;

use qpf;
use qpf\base\Core;

/**
 * File Session 处理程序
 */
class File extends Core implements \SessionHandlerInterface
{
    /**
     * 保存路径
     * @var string
     */
    protected $path = '';
    /**
     * 使用子目录
     * @var bool
     */
    protected $subdir = true;
    /**
     * 是否压缩数据
     * @var bool
     */
    protected $compress = false;
    /**
     * gc回收判定多少秒未更新
     * @var int
     */
    protected $gcMaxlifetime = 1440;
    /**
     * gc执行概率
     * @var int
     */
    protected $gcDivisor = 1000;
    /**
     * 有效期, 保存多少秒
     * @var int
     */
    protected $expire = 0;
    
    /**
     * 初始化
     */
    protected function init()
    {
        if (empty($this->path)) {
            $this->path = QPF::$app->getRuntimePath(true) . '/session';
        }
    }
    
    /**
     * 打开Session
     * @param string $save_path 保存路径
     * @param string $session_name 会话ID
     * @return bool 是否成功
     */
    public function open($save_path, $session_name)
    {
        try {
            !is_dir($this->path) && mkdir($this->path, 0755, true);
        } catch (\Exception $e) {
            return false;
        }
        
        if (mt_rand(1, $this->gcDivisor) == 1) {
            $this->gc($this->gcMaxlifetime);
        }
        
        return true;
    }

    /**
     * 关闭Session
     * @return bool
     */
    public function close()
    {
        return true;
    }

    /**
     * 读取Session并验证是否有效
     * @param string $session_id 会话ID
     * @return mixed
     */
    public function read($session_id)
    {
        $filename = $this->getFileName($session_id);
        
        if (!is_file($filename)) {
            // session 不存在
            return '';
        }

        $content = file_get_contents($filename);
        
        if ($content === false) {
            return '';
        }
        
        $expire = (int) substr($content, 8, 12);
        // 缓存过期
        if ($expire != 0 && time() > filemtime($filename) + $expire) {
            unlink($filename);
        }
        
        $content = substr($content, 32);
        
        // 解压数据
        if ($this->compress && function_exists('gzcompress')) {
            $content = gzuncompress($content);
        }
        
        return $content;
    }

    /**
     * 写入Session数据
     * @param string $session_id 会话ID
     * @param string $session_data 会话数据
     * @return bool
     */
    public function write($session_id, $session_data)
    {
        $expire = $this->getExpireTime($this->expire);
        $filename = $this->getFileName($session_id, true);
        
        // 压缩数据
        if ($this->compress && function_exists('gzcompress')) {
            $session_data = gzcompress($session_data, 3);
        }
        
        $session_data = "<?php\n//" . sprintf('%012d', $expire) . "\n exit();?>\n" . $session_data;
        $result = file_put_contents($filename, $session_data);
        
        if ($result) {
            // 清除文件状态缓存
            clearstatcache();
            return true;
        }
        
        return false;
    }

    /**
     * 删除Session数据
     * @param string $session_id
     * @return bool
     */
    public function destroy($session_id)
    {
        $filename = $this->getFileName($session_id);
        try {
            return is_file($filename) && unlink($filename);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 回收超时Session数据
     * 
     * 将删除最近几秒未更新的会话
     * @param int $maxlifetime 由系统传入, 默认值`1440`, session.gc_maxlifetime
     * @return bool
     */
    public function gc($maxlifetime)
    {
        $maxlifetime = $this->gcMaxlifetime;
        $list = glob($this->path . '/*');
        
        foreach ($list as $path) {
            if (is_dir($path)) {
                $files = glob($path . '/*.php');
                foreach ($files as $file) {
                    if (time() > filemtime($file) + $maxlifetime) {
                        unlink($file);
                    }
                }
            } elseif (time() > filemtime($file) + $maxlifetime) {
                unlink($path);
            }
        }
        
        return true;
    }
    
    /**
     * 获取变量的存储文件名
     * @param string $name 会话ID
     * @param bool $auto 是否自动创建目录, 默认`false`
     */
    protected function getFileName($name, $auto = false)
    {
        // 使用分级目录
        if ($this->subdir) {
            $name = substr($name, 0, 2) . '/sess_' . $name;
        } else {
            $name = 'sess_' . $name;
        }
        
        $filename = $this->path . '/' . $name . '.php';
        $dir = dirname($filename);
        
        if ($auto && !is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        
        return $filename;
    }
    
    /**
     * 获取有效期
     * @param int|\DateTimeInterface $expire 有效期
     * @return int
     */
    protected function getExpireTime($expire)
    {
        if ($expire instanceof \DateTimeInterface) {
            $expire = $expire->getTimestamp() - time();
        }
        
        return (int) $expire;
    }
    
}