<?php
namespace qpf\log\storage;

use qpf;

/**
 * 文件存储器
 * 
 * 存储规则:
 * 1. 基础日期模式 
 * `@path/年月/日.ext`, 例`1991-01-01` => `@path/199101/01.ext`
 * - 当[[$max]]的值为`0`时, 代表开启, 以月份目录来保存日志.
 * 2. 文件碎片模式
 * `@path/年月日.ext`, 例`1991-01-01` => `@path/19910101.ext`
 * - 当[[$max]]的值大于`0`时, 代表开启. 值代表文件碎片数量上限. 超过将自动清除最早的文件碎片.
 * 3. 日志级别分离
 * - 上面两种模式都可实现, 将特定的日志级别单独保存, 通过设置[[$alone]]属性指定需单独保存的级别名称.
 * 例如  `$alone = ['info', 'debug']` :
 * info级别: '@path/199101/01_info.ext' 或 `@path/19910101_info.ext`
 * debug级别: '@path/199101/01_debug.ext' 或 `@path/19910101_debug.ext`
 * - 注意当日志格式启用json时, 将自动关闭日志级别分离模式.
 */
class File extends LogStorage
{
    /**
     * 时间格式
     * @var string
     */
    protected $time = 'Y-m-d H:i:s';
    /**
     * 单文件容量上限(KB)
     * @var int
     */
    protected $size = 2097152;
    /**
     * 文件碎片模式
     * - 关闭需设置为`0`, 开启只需值大于`0`, 即碎片上限数量.
     * - 当文件碎片数量大于该属性的值. 将删除最旧的一个文件碎片.
     * - 推荐 :  天数 * 2 , 天数 + (天数/2)
     * @var int
     */
    protected $max = 0;
    /**
     * 单独保存的日志级别
     * - 需全部分离时, 设置该值为(bool) `true`.
     * @var array
     */
    protected $alone = [];
    /**
     * 文件路径
     * @var string
     */
    protected $path;
    /**
     * 日志扩展名
     * @var string
     */
    protected $ext = '.log';
    /**
     * 目录权限
     * @var integer
     */
    protected $mode = 0775;
    /**
     * 是否将消息转换为json格式
     * @var bool
     */
    protected $json = false;
    /**
     * 是否额外附加环境信息
     * @var bool
     */
    protected $extra = false;

    /**
     * 日志存储实现
     */
    public function save()
    {
        // 获得日志文件名
        $filename = $this->getName();
        
        // 准备目录
        $dir = dirname($filename);
        !is_dir($dir) && mkdir($dir, $this->mode, true);
        
        $info = [];

        // $this->log[$level] => [$msg1, $msg2]
        foreach ($this->log as $level => $list) {
            foreach ($list as $msg) {
                if(!is_string($msg)) {
                    $msg = get_varstr($msg);
                }
                
                $info[$level][] = $this->json ? $msg : '[' . $level . ']' . $msg;
            }
            
            // 单独保存的日志级别, 启用消息json格式时无法使用
            if(!$this->json && ($this->alone === true || in_array($level, $this->alone))) {
                $aloneName = $this->getAloneName($dir, $level);
                $this->write($info[$level], $aloneName, true);
                unset($info[$level]);
            }
        }
        
        if (empty($info)) {
            return true;
        }
        
        return $this->write($info, $filename, false);
    }
    
    /**
     * 写入日志到文件
     * @param array $message 日志列表
     * @param string $filename 文件名称
     * @param bool $alone 是否单独文件写入
     */
    protected function write(array $message, $filename, $alone = false)
    {
        // 日志记录时间
        $info['timestamp'] = date($this->time);
        
        foreach ($message as $level => $list) {
            $info[$level] = is_array($list) ? implode("\r\n", $list) : $list;
        }
        
        if (PHP_SAPI == 'cli') {
            $message = $this->parseCLI($info);
        } else {
            $this->extraInfo($info, $alone);
            $message = $this->parseCGI($info);
        }
        
        return error_log($message, 3, $filename);
    }
    
    /**
     * 获取日志文件名
     * @return string
     */
    public function getName()
    {
        // 运行环境类型
        $spai = PHP_SAPI == 'cli' ? '_cli' : '';
        
        if ($this->max > 0) {
            // 文件碎片自动清理
            $logs = glob($this->getPath() . DIRECTORY_SEPARATOR . '*' . $this->ext);
            if($logs !== false && count($logs) > $this->max) {
                unlink($logs[0]);
            }
            $name = date('Ymd') . $spai . $this->ext;
        } else {
            $name = date('Ym') . DIRECTORY_SEPARATOR . date('d') . $spai . $this->ext;
        }

        return $this->getPath() . DIRECTORY_SEPARATOR . $name;
    }

    /**
     * 获取单独保存的日志文件名
     * @param string $level 日志级别
     * @return string
     */
    public function getAloneName($path, $level)
    {
        // 运行环境类型
        $spai = PHP_SAPI == 'cli' ? '_cli' : '';
        
        if($this->max > 0) {
            $name = date('Ymd') . '_' . $level . $spai;
        } else {
            $name = date('d') . '_' . $level . $spai;
        }
        
        return $path . DIRECTORY_SEPARATOR . $name . $this->ext;
    }
    
    /**
     * 获得日志存储位置
     * @return string
     */
    public function getPath()
    {
        if($this->path === null) {
            $this->path = QPF::app()->getRuntimePath() . DIRECTORY_SEPARATOR . 'logs';
        }
        
        return $this->path;
    }
    
    /**
     * 设置日志存储位置
     * @param string $path
     * @return void
     */
    public function setPath($path)
    {
        if (strpos($path, '@') === 0) {
            $this->path = QPF::getAlias($path);
        } else {
            $this->path = $path;
        }
    }
    
    /**
     * 解析CLI日志
     * @param array $info 日志信息
     * @return string
     */
    protected function parseCLI(array $info)
    {
        if ($this->json) {
            $message = json_encode($info, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\r\n";
        } else {
            $timestamp = $info['timestamp'];
            unset($info['timestamp']);
            $message = '[' . $timestamp . ']' . implode("\r\n", $info) . "\r\n";
        }
        
        return $message;
    }
    
    /**
     * 解析CGI日志
     * @param array $info 日志信息
     * @return string
     */
    protected function parseCGI(array $info)
    {
        $request = QPF::app()->requeset();
        $envInfo = [
            'ip'        => $request->ip(),
            'method'    => $request->method(),
            'host'      => $request->host(),
            'url'       => $request->url(),
        ];
        
        if ($this->json) {
            $info = $envInfo + $info;
            return json_encode($info, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\r\n";
        }
        
        array_unshift($info, "---------------------------------------------------------------\r\n[{$info['timestamp']}] {$envInfo['ip']} {$envInfo['method']} {$envInfo['host']}{$envInfo['uri']}");
        unset($info['timestamp']);
        
        return implode("\r\n", $info) . "\r\n";
    }
    
    /**
     * CGI模式附加额外信息
     * @param array $info 日志信息
     * @param bool $alone 是否是单独日志级别写入
     * @return void
     */
    protected function extraInfo(&$info, $alone)
    {
        // 必须启用了调式模式, 并且当前日志存储允许额外附加环境信息
        if(QPF::app()->isDebug() && $this->extra) {
            // 启用了消息json格式化.
            if ($this->json) {
                $runtime = round(microtime(true) - QPF::app()->getStartTime(), 10);
                $reqs = $runtime > 0 ? number_format(1 / $runtime, 2) : '∞';
                $memuse = number_format((memory_get_usage() - QPF::app()->getStartMem()) / 1024, 2);
                
                $info = [
                    'runtime'   => number_format($runtime, 6) . 's',
                    'reqs'    => $reqs . 'req/s',
                    'memory'  => $memuse . 'kb',
                    'file'    => count(get_included_files()),
                ] + $info;
            } elseif(!$alone) {
                // 当不是单独日志级别写入时, 额外符集环境信息
                $runtime = round(microtime(true) - QPF::app()->getStartTime(), 10);
                $reqs = $runtime > 0 ? number_format(1 / $runtime, 2) : '∞';
                $memuse = number_format((memory_get_usage() - QPF::app()->getStartMem()) / 1024, 2);
                
                $runtime = '[运行时间：' . number_format($runtime, 6) . 's] ';
                $runtime .= '[吞吐率：' . $reqs . 'req/s]';
                $memuse = ' [内存消耗：' . $memuse . 'kb]';
                $reqs = ' [文件加载：' . count(get_included_files()) . ']';
                
                array_unshift($info, $runtime, $memuse, $reqs);
            }
        }
    }
}