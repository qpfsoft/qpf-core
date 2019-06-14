<?php
namespace qpf\htmc\html;

/**
 * Audio 标签定义声音，比如音乐或其他音频流 - h5
 *
 * - Internet Explorer 8 以及更早的版本不支持该标签。
 * - 可以在开始标签和结束标签之间放置文本内容，这样老的浏览器就可以显示出不支持该标签的信息。
 * 
 * 特性值:
 * currentTime 以s为单位返回从开始播放到目前所花的时间，也可设置currentTime的值来跳转到特定位置.
 * 
 * firefox 和 opera 支持 ogg 音频.
 * safari 和 ie 支持 mp3.
 * Google的chrome都支持.
 * 
 * 示例:
 * ~~~
 * # 最少的代码
 * <audio src="song.ogg" controls="controls"></audio>
 * 
 * # 尽量兼容浏览器的写法
 * <audio controls="controls">
 * <source src="song.ogg" type="audio/ogg">
 * <source src="song.mp3" type="audio/mpeg">
 * 您的浏览器不支持 audio 标签。
 * </audio>
 * ~~~
 * 
 * 
 * preload 预先加载音频:
 * 例如, 脸书或博客. 博文是视频, 设置不设置预先加载, 对网络影响很大.
 * 
 * 属性:
 * - none : 指用户不需要对音频预先加载, 这样可以减少网络流量.
 *          如果每一篇文章其实都是音频.但只有当用户确认打开这些音频收听时，才通过网络进行加载。
 *          否则，试想一下，这么多数量的音频同时进行预加载，速度是相当慢的。
 * - meta : 选项告诉服务器, 不想马上播放, 只获得音频的元数据信息(比如文件的大小，时长等)。
 *          如果开发者是在设计音频播放器或者需要获得音频的信息而不需要马上播放视频，则可以使用这个选项。
 * - auto : 选项告诉服务端，用户需要马上加载音频并进行流式播放，这在比如一些游戏场景等需要实时音频的场景中会用到。
 * @author qiun
 *
 */
class Audio extends HtmlBase
{
    /**
     * 标签名称
     * @var string
     */
    protected $tagName = 'audio';
    
    /**
     * 设置要播放的音频URL - h5
     * 
     * - 也可以使用  source 标签 来设置音频。
     * @param string $value url
     * @return $this
     */
    public function src($value)
    {
        $this->attr(['src' => $value]);
        return $this;
    }
    
    /**
     * 设置一旦音频就绪马上开始播放 - h5
     * @return $this
     */
    public function autoplay()
    {
        $this->attr(['autoplay' => 'autoplay']);
        return $this;
    }
    
    /**
     * 设置使用游览器默认播放控件 - h5
     * 
     * 控件包括: 播放,暂停,定位,音量,全屏切换
     * 如果可用: 字母, 音轨
     * @return $this
     */
    public function controls()
    {
        $this->attr(['controls' => 'controls']);
        return $this;
    }

    /**
     * 设置音频将循环播放 - h5
     * @return $this
     */
    public function loop()
    {
        $this->attr(['loop' => 'loop']);
        return $this;
    }
    
    /**
     * 设置为静音 - h5
     * 
     * - 视频输出应该被静音
     * - iE9不支持
     * @return $this
     */
    public function muted()
    {
        $this->attr(['muted' => 'muted']);
        return $this;
    }
    
    /**
     * 设置是否预加载音频
     * 
     * - 如果设置了 autoplay 属性，则忽略该属性。
     * @param string $value 可能的值:
     * - auto : 当页面加载后载入整个音频
     * - meta : 当页面加载后只载入元数据
     * - none : 当页面加载后不载入音频
     * @return $this
     */
    public function preload($value)
    {
        $this->attr(['preload' => $value]);
        return $this;
    }
}