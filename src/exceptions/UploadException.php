<?php
namespace qpf\exceptions;

/**
 * 上传文件异常
 */
class UploadException extends Exception
{
    /**
     * 构造函数
     * @param int $code 上传错误码, `$_FILES['userfile']['error']`
     * @param string $message 自定义消息
     */
    public function __construct($code, $message = null)
    {
        if($message === null) {
            $message = static::getUploadError($code);
        }
        
        parent::__construct($message, $code);
    }
    
    /**
     * 获取错误ID对应的错误消息
     * @link http://www.php.net/manual/zh/features.file-upload.errors.php
     * @param int $code 上传错误码, `$_FILES['userfile']['error']`
     * @return string
     */
    public static function getUploadError($code)
    {
        switch ($code) {
            case UPLOAD_ERR_OK:
                $message = 'upload file success';
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $message = 'upload file size exceeds the maximum value';
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = 'only the portion of file is uploaded';
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = 'no file to uploaded';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = 'upload temp dir not found';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = 'file write error';
                break;
            default:
                $message = 'unknown upload error';
        }
        
        return $message;
    }
    
    /**
     * 获取异常名称
     * @return string 返回字符串描述的该异常名称
     */
    public function getName()
    {
        return 'Upload Excetion';
    }
}