<?php
namespace qpf\file;

/**
 * UploadFile 代表一个上传的文件对象
 */
class UploadFiles extends \SplFileObject
{
    /**
     * 完整文件路径名
     * @var string
     */
    protected $filename;
    /**
     * 文件保存名称
     * @var string
     */
    protected $newname;
    /**
     * 文件命名规则
     * - 属性值为哈希类型字符串或回调函数
     * @var string|callable 可能的值:
     * - date : 根据日期和微秒数生成
     * - md5  : 对文件使用md5_file散列生成
     * - sha1 : 对文件使用sha1_file散列生成
     */
    protected $rule = 'date';
    /**
     * 文件验证规则
     * 
     * 规则示例:
     * ```
     * [
     *      'size'  => , // 上传文件的最大字节
     *      'type'  => 'image/gif', // 文件MIME类型，多个用逗号分割或者数组
     *      'ext'   => 'jpg,jpeg,gif,png,zip,rar,doc,txt,pem'; 文件后缀，多个用逗号分割或者数组
     * ]
     * ```
     * @var array
     */
    protected $validate = [];
    /**
     * 文件哈希
     * ```
     * [
     *      '哈希类型'  => '哈希值',
     * ]
     * ```
     * @var array
     */
    protected $hash = [];
    /**
     * 文件信息
     * @var array
     */
    protected $info = [];
    /**
     * 是否开启测试模式
     * @var bool
     */
    protected $test;
    
    /**
     * 验证错误的信息
     * @var string
     */
    private $error;
    
    /**
     * 构造函数
     * @param string $filename 要读取的文件
     * @param array $config 对象配置数组
     */
    public function __construct($filename, $config = [])
    {
        if(isset($config['mode'])) {
            $mode = $config['mode'];
            unset($config['mode']);
        } else {
            $mode = 'r';
        }
        
        parent::__construct($filename, $mode);
        
        foreach ($config as $name => $value) {
            if (property_exists($this, $name)) {
                $this->{$name} = $value;
            }
        }
        
        $this->init();
    }
    
    /**
     * 初始化
     */
    public function init()
    {
        $this->filename = $this->getRealPath() ?: $this->getPathname();
        
        if (empty($this->test)) {
            $this->test = false;
        }
    }
    
    /**
     * 设置测试模式是否开启
     * @param bool $test
     * @return $this
     */
    public function test($test = false)
    {
        $this->test = $test;
        
        return $this;
    }
    
    /**
     * 设置上传信息
     * @param array $info 上传文件信息
     * @return $this
     */
    public function setInfo(array $info)
    {
        $this->info = $info;
        
        return $this;
    }
    
    /**
     * 获取上传文件信息
     * @param string $name
     * @return array|string|null
     */
    public function getInfo($name = null)
    {
        if ($name === null) {
            return $this->info;
        }
        
        return isset($this->info[$name]) ? $this->info[$name] : null;
    }
    
    /**
     * 获取上传文件的文件名
     * @return string
     */
    public function getNewname()
    {
        return $this->newname;
    }
    
    /**
     * 设置上传文件的新文件名
     * @param string $name
     * @return $this
     */
    public function setNewname($name)
    {
        $this->newname = $name;
        
        return $this;
    }
    
    /**
     * 获取文件的哈希散列值
     * @param string $type
     * @return string
     */
    public function hash($type = 'sha1')
    {
        if (!isset($this->hash[$type])) {
            $this->hash[$type] = hash_file($type, $this->filename);
        }
        
        return $this->hash[$type];
    }
    
    /**
     * 获取文件类型信息
     * @return string
     */
    public function mime()
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $this->info);
        finfo_close($finfo);
        
        return $mime;
    }
    
    /**
     * 设置文件命名规则
     * @param mixed $rule 文件命名规则
     * @return $this
     */
    public function rule($rule)
    {
        $this->rule = $rule;
        
        return $this;
    }
    
    /**
     * 设置上传文件的验证规则
     * @param array $rule 验证规则
     * @return $this
     */
    public function validate(array $rule = [])
    {
        $this->validate = $rule;
        
        return $this;
    }
    
    /**
     * 返回验证规则
     * @return array
     */
    public function getValidate(array $rule = [])
    {
        if (empty($rule)) {
            $rule = $this->rule;
        }
        
        return $rule;
    }
    
    /**
     * 是否合法的上传文件
     * @return bool
     */
    public function isUpload()
    {
        if($this->test) {
            return is_file($this->filename);
        }
        
        return is_uploaded_file($this->filename);
    }
    
    /**
     * 使用规则检查上传文件是否合法
     * @param array $rule 验证规则
     * @return bool
     */
    public function check(array $rule = [])
    {
        $rule = $this->getValidate($rule);
        
        if((isset($rule['size']) && !$this->checkSize($rule['size']))
            || (isset($rule['type']) && !$this->checkMime($rule['type']))
            || (isset($rule['ext']) && !$this->checkExt($rule['ext']))
            || !$this->checkImg()) {
                return false;
            }
            
            return true;
    }
    
    /**
     * 返回指定文件的后缀名
     * @param string $path
     * @return string
     */
    public function getExt($path)
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }
    
    /**
     * 检查上传文件后缀名是否允许
     * @param array|string $allow 允许的后缀, 字符串逗号分割
     * @return bool
     */
    public function checkExt($allow)
    {
        $allow = is_string($allow) ? explode(',', $allow) : $allow;
        
        $ext = $this->getExt($this->getInfo('name'));
        
        if (!in_array($ext, $allow)) {
            $this->error = 'upload file type not allow';
            return false;
        }
        
        return true;
    }
    
    /**
     * 上传文件为图片时, 检查图片文件是否合法
     * @return bool
     */
    public function checkImg()
    {
        $ext = $this->getExt($this->getInfo('name'));
        
        $extType = ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf'];
        $imgType = [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_SWF, IMAGETYPE_BMP, IMAGETYPE_SWC];
        
        if (in_array($ext, $extType) && !in_array($this->getImageType($this->filename), $imgType)) {
            $this->error = 'illegal image files';
            return false;
        }
        
        return true;
    }
    
    /**
     * 获取图片类型
     * @param string $filename 文件
     * @return string|false 不是图片返回false
     */
    protected function getImageType($filename)
    {
        if (function_exists('exif_imagetype')) {
            if (filesize($filename) > 11) {
                return exif_imagetype($filename);
            } else {
                return false;
            }
        } else {
            $exif_imagetype = function ($filename) {
                if ( ( list($width, $height, $type, $attr) = getimagesize( $filename ) ) !== false ) {
                    return $type;
                }
                return false;
            };
            
            return $exif_imagetype($filename);
        }
    }
    
    /**
     * 检查上传文件的大小
     * ```
     * 1kb = 1024字节
     * 1mb = 1024kb
     * ```
     * @param int $size max_size 字节
     * @return bool
     */
    public function checkSize($size)
    {
        if ($this->getSize() > $size) {
            $this->error = 'filesize not match';
            return false;
        }
        
        return true;
    }
    
    /**
     * 检查上传文件的媒体类型
     * @param array|string $allow 允许的后缀, 字符串逗号分割
     * @return bool
     */
    public function checkMime($allow)
    {
        $allow = is_string($allow) ? explode(',', $allow) : $allow;
        
        if (!in_array(strtolower($this->mime()), $allow)) {
            $this->error = 'upload file mimetype not allow';
            return false;
        }
        
        return true;
    }
    
    /**
     * 检查目录是否可写, 不存在时自动创建
     * @param string $path 目录
     * @return bool
     */
    public function checkPath($path)
    {
        if (is_dir($path)) {
            return true;
        }
        
        if (mkdir($path, 0755, true)) {
            return true;
        }
        
        $this->error = 'Update file save directory create failed';
        return false;
    }
    
    /**
     * 移动保存上传文件
     * @param string $path 保存路径
     * @param string|bool $newname 保存的文件名, 默认`true`自动生成,
     * 值为false或""将保留文件原名
     * @param bool $overwrite 是否覆盖重名文件, 默认`true`
     * @return false|$this
     */
    public function move($path, $newname = true, $overwrite = true)
    {
        // 上传文件失败
        if (!empty($this->info['error'])) {
            $this->error($this->info['error']);
            return false;
        }
        
        // 检查上传合法性
        if (!$this->isUpload()) {
            $this->error = 'upload illegal files';
        }
        
        // 检查文件合法性
        if (!$this->check()) {
            return false;
        }
        
        $path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        // 根据命名规则生成新文件名
        $newname = $this->buildNewname($newname);
        $filename = $path . $newname;
        
        // 检查保存目录, 自动创建
        if ($this->checkPath(dirname($filename)) === false) {
            return false;
        }
        
        // 不允许覆盖时, 提示重名文件
        if (!$overwrite && is_file($filename)) {
            $this->error = 'Upload files save name has exists :'. $newname;
            return false;
        }
        
        // 从临时目录移动到保存目录
        if ($this->test) {
            rename($this->filename, $filename);
        } elseif (!move_uploaded_file($this->filename, $filename)) {
            $this->error = 'upload write error';
            return false;
        }
        
        return new static($filename, [
            'mode'      => 'r',
            'newname'   => $newname,
            'info'      => $this->info,
        ]);
    }
    
    /**
     * 生成新文件名
     * @param string|bool $newname 文件名, `true`自动生成, `false`原名
     * @return string
     */
    protected function buildNewname($newname)
    {
        // 自动生成
        if ($newname === true) {
            $newname = $this->autoBuildName();
            // 使用原名
        } elseif ($newname === false || $newname === '') {
            $newname = $this->getInfo('name');
        }
        
        // 自动添加后缀
        if(!strpos($newname, '.')) {
            $newname .= '.' . $this->getExt($this->getInfo('name'));
        }
        
        return $newname;
    }
    
    /**
     * 自动生成文件名
     * @return string
     */
    protected function autoBuildName()
    {
        if ($this->rule instanceof \Closure) {
            $name = call_user_func_array($this->rule, [$this]);
        } else {
            switch ($this->rule) {
                case 'date':
                    $name = date('Ymd') . DIRECTORY_SEPARATOR . md5(microtime(true));
                    break;
                default:
                    if (in_array($this->rule,  hash_algos())) {
                        $hash = $this->hash($this->rule);
                        $name =  substr($hash, 0, 2) . DIRECTORY_SEPARATOR . substr($hash, 2);
                    } elseif (is_callable($this->rule)) {
                        $name = call_user_func($this->rule);
                    } else {
                        $name = date('Ymd') . DIRECTORY_SEPARATOR . md5(microtime(true));
                    }
            }
        }
        
        return $name;
    }
    
    /**
     * 设置上传错误信息
     * @param int $code 错误码
     * @return void
     */
    private function error($code)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $this->error = 'upload File size exceeds the maximum value';
                break;
            case UPLOAD_ERR_PARTIAL:
                $this->error = 'only the portion of file is uploaded';
                break;
            case UPLOAD_ERR_NO_FILE:
                $this->error = 'no file to uploaded';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $this->error = 'upload temp dir not found';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $this->error = 'file write error';
                break;
            default:
                $this->error = 'unknown upload error';
        }
    }
    
    /**
     * 返回上传失败的错误信息
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
    
    /**
     * 支持快捷文件哈希散列值获取
     * @param string $method 方法名
     * @param mixed $args
     * @return string
     */
    public function __call($method, $args)
    {
        return $this->hash($method);
    }
}