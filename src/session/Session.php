<?php
namespace qpf\session;

use qpf;
use qpf\base\Core;
use qpf\exception\NotFoundException;

/**
 * Session 管理器
 */
class Session extends Core
{
    /**
     * 配置选项
     * @var array
     */
    protected $config = [];
    /**
     * 是否已开启session
     * @var bool
     */
    protected $start = false;
    /**
     * 是否启用锁机制
     * @var bool
     */
    protected $lock = false;
    /**
     * 锁处理程序
     * @var object
     */
    protected $lockHandler;
    /**
     * 锁key
     * @var string
     */
    protected $lockKey = 'PHPSESSID';
    /**
     * 锁自动失效时间, 秒
     * @var int
     */
    protected $lockTimeout = 3;
    /**
     * 会话处理程序支持列表
     * @var array
     */
    protected $handler;
    
    /**
     * 初始化
     */
    public function init()
    {
        $this->config = QPF::$app->config->group('session');

        // 客户端不支持cooike
        if (isset($this->config['var_session_id']) && isset($_REQUEST[$this->config['var_session_id']])) {
            session_id($_REQUEST[$this->config['var_session_id']]);
        } elseif (isset($this->config['id']) && !empty($this->config['id'])) {
            session_id($this->config['id']);
        }
        
        // 注册session处理程序
        if (!empty($this->config['type'])) {
            $class = strpos($this->config['type'], '\\') !== false ? $this->config['type'] : '\\qpf\\session\\type\\' . ucwords($this->config['type']);
            
            if(!class_exists($class) || !session_set_save_handler(new $class($this->config))) {
                throw new NotFoundException('session handler class error' . $class, 'class');
            }
        }
        
        if ($this->config['auto_start'] == true) {
            $options = isset($this->config['options']) ? $this->config['options'] : [];
            try {
                session_start($options);
            } catch (\Exception $e) {
            }
            $this->start = true;
        }
    }
    
    /**
     * 确保启动session
     * @return void
     */
    public function start()
    {
        if ($this->start === false) {
            if (PHP_SESSION_ACTIVE != session_status()) {
                session_start();
            }
            $this->start = true;
        }
    }
    
    /**
     * 设置Session变量
     * @param string $name 名称, 支持点设置二维数组值
     * @param mixed $value 值
     * @return void
     */
    public function set($name, $value)
    {
        $this->lock();$this->start();
        
        // 二维数组赋值
        if (strpos($name, '.')) {
            list($name1, $name2) = explode('.', $name);
            $_SESSION[$name1][$name2] = $value;
        } else {
            $_SESSION[$name] = $value;
        }
        
        $this->unlock();
    }
    
    /**
     * 获取session变量
     * @param string $name 名称, 支持点获取二维数组值
     * @param mixed $default 默认值
     * @return mixed
     */
    public function get($name = null, $default = null)
    {
        $this->lock();$this->start();
        
        $value = $_SESSION;
        
        if ($name === null) {
            return $value;
        } elseif (!empty($name)) {
            $name = explode('.', $name);
            
            foreach ($name as $val) {
                if (isset($value[$val])) {
                    $value = $value[$val];
                } else {
                    $value = $default;
                    break;
                }
            }
        } else {
            $value = $default;
        }
        
        $this->unlock();
        
        return $value;
    }
    
    /**
     * 判断指定的session参数是否存在
     * @param string $name 名称
     * @return bool
     */
    public function has($name)
    {
        $this->start();
        
        $session = $_SERVER;
        
        $name = explode('.', $name);
        
        foreach ($name as $val) {
            if (!isset($session[$val])) {
                return false;
            } else {
                $session = $session[$val];
            }
        }
        
        return true;
    }

    /**
     * 添加数据到数组类型的session参数中
     * @param string $name 名称
     * @param mixed $value 值
     */
    public function push($name, $value)
    {
        $result = $this->get($name);
        
        if ($result === null) {
            $result = [];
        }
        
        // 防止无意义重复添加
        if(!in_array($value, $result)) {
            $result[] = $value;
        }
        
        $this->set($name, $result);
    }
    
    /**
     * 获取并删除指定session参数
     * @param string $name 名称
     * @return mixed
     */
    public function pull($name)
    {
        $result = $this->get($name);
        
        if ($result) {
            $this->delete($name);
            return $result;
        }
    }
    
    /**
     * 设置session临时参数, 在下一次请求时, 可通过[[flush()]]清除所有临时参数
     * ```
     * $_SESSION = [
     *      'var'       => 'val',
     *      '__flash__' => [
     *          0           => 'var',
     *          '__time__'  => 574122311.122,
     *      ],
     * ],
     * ```
     * @param string $name 名称
     * @param string $value 值
     * @return void
     */
    public function flash($name, $value)
    {
        $this->set($name, $value);
        
        if (!$this->has('__flash__.__time__')) {
            $this->set('__flash__.__time__', $_SERVER['REQUEST_TIME_FLOAT']);
        }

        $this->push('__flash__', $name);
    }
    
    /**
     * 清空当前请求的session临时参数
     * @return void
     */
    public function flush()
    {
        if ($this->start === false) {
            return;
        }
        
        $flash = $this->get('__flash__');
        
        if (!empty($flash)) {
            $time = $flash['__time__'];
            
            if ($_SERVER['REQUEST_TIME_FLOAT'] > $time) {
                unset($flash['__time__']);
                $this->delete($flash);
                $this->set('__flash__', []);
            }
        }
    }
    
    /**
     * 删除session参数
     * @param array|string $name 名称, 支持点删除二维数组值
     * @return void
     */
    public function delete($name)
    {
        $this->start();
        
        if (is_array($name)) {
            foreach ($name as $id) {
                $this->delete($id);
            }
        } elseif (strpos($name, '.')) {
            list($name1, $name2) = explode('.', $name);
            unset($_SESSION[$name1][$name2]);
        } else {
            unset($_SESSION[$name]);
        }
    }
    
    /**
     * 清空session中的所有数据
     * @return void
     */
    public function clear()
    {
        $this->start();$_SESSION = [];
    }
    
    /**
     * 开启session读写加锁
     */
    protected function lock()
    {
        if (!$this->lock) {
            return;
        }
        
        $handler = $this->getLockHandler();
        if($handler) {
            $start = time();
            // 使用cookie的session_id来实现互斥, 第一次请求没有session_id
            $id = isset($_COOKIE[$this->lockKey]) ? $_COOKIE[$this->lockKey] : '';
            
            do {
                if (time() - $start > $this->lockTimeout) {
                    $this->unlock();
                }
            } while (!$handler->lock($id, $this->lockTimeout));
        }
    }
    
    /**
     * 解除session读写锁
     * @return void
     */
    protected function unlock()
    {
        if (!$this->lock) {
            return;
        }
        
        $this->pause();
        
        $handler = $this->getLockHandler();
        if($handler) {
            $id = isset($_COOKIE[$this->lockKey]) ? $_COOKIE[$this->lockKey] : '';
            $handler->unlock($id);
        }
    }
    
    /**
     * 获取读写锁处理程序
     */
    protected function getLockHandler()
    {
        if ($this->lockHandler === null && !empty($this->config['type'])) {
            $class = $this->handler[$this->config['type']];
            $this->lockHandler = QPF::create($class);
        }
        
        return $this->lockHandler;
    }
    
    /**
     * 销毁session
     * @return void
     */
    public function destroy()
    {
        $_SESSION = [];
        
        // 释放所有的会话变量
        session_unset();
        // 销毁一个会话中的全部数据
        session_destroy();
        
        $this->start = false;
        $this->lockHandler = null;
    }
    
    /**
     * 重新生成session_id
     * @param bool $delete 是否删除原session_id所关联的会话存储文件
     * @return void
     */
    public function regenerate($delete = false)
    {
        session_regenerate_id($delete);
    }
    
    /**
     * 写入session并暂停
     */
    public function pause()
    {
        // 写入session并且结束session
        session_write_close();
        $this->start = false;
    }
}