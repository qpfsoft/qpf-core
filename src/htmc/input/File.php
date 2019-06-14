<?php
namespace qpf\htmc\input;

/**
 * Input元素`file`上传类型
 * 
 * 定义输入字段和 "浏览"按钮，供文件上传
 * @author qiun
 *
 */
class File extends InputBase
{
    /**
     * 规定input元素的类型
     * @var string
     */
    protected $inputType = 'file';

    /**
     * 可选择多个文件进行上传 - h5
     * @return $this
     */
    public function multiple()
    {
        $this->attr(['multiple' => 'multiple']);
        return $this;
    }

    /**
     * 规定上传文件的类型
     * 
     * - 不限制图像格式, 即`accept="image/*"`
     * - 在服务器一定也要验证
     * - 'audio/*', 'video/*', 'image/*'
     * @param string $value 用逗号隔开的 MIME 类型列表
     * @return $this
     */
    public function accept($value)
    {
        $this->attr(['accept' => $value]);
        return $this;
    }
}