<?php
namespace qpf\image\lib;

/**
 * HSV颜色模型
 * 
 * 这个模型中颜色的参数分别是：色调（H），饱和度（S），明度（V）
 * 
 * # 色调H
 * 取值范围为0°～360°，从红色开始按逆时针方向计算，红色为0°，绿色为120°,蓝色为240°。它们的补色是：黄色为60°，青色为180°,品红为300°
 * 
 * # 饱和度S
 * 取值范围为0%～100%，值越大，颜色越饱和, 饱和度高，颜色则深而艳
 * 
 * # 明度V
 * 取值范围为0%（黑）到100%（白）
 */
class HSV
{

    /**
     * 色相 0-260
     * @var float
     */
    protected $_hue;

    /**
     * 饱和度 0-1
     * @var float
     */
    protected $_saturation;

    /**
     * 色调 0-1
     * @var float
     */
    protected $_value;

    /**
     * 构造
     * @param float $hue 色相 0-260
     * @param float $saturation 饱和度 0-1, 例`0.75`
     * @param float $value 色调  0-1
     */
    public function __construct($hue = 0, $saturation = 0, $value = 0)
    {
        $this->_hue = $hue;
        $this->_saturation = $saturation;
        $this->_value = $value;
    }

    /**
     * 获取色相 0-360
     * @return float
     */
    public function getHue()
    {
        return $this->_hue;
    }

    /**
     * 获取饱和度 0-1
     * @return float
     */
    public function getSaturation()
    {
        return $this->_saturation;
    }

    /**
     * 获取色调 0-1
     * @return float
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * 返回该色彩在RGB色彩空间的描述
     * @return RGB
     */
    public function toRGB()
    {
        $hue = $this->getHue();
        $saturation = $this->getSaturation();
        $value = $this->getValue();
        $hi = floor($hue / 60) % 6;
        $f = $hue / 60 - $hi;
        $p = $value * (1 - $saturation);
        $q = $value * (1 - $f * $saturation);
        $t = $value * (1 - (1 - $f) * $saturation);
        switch ($hi) {
            case 0:
                $red = $value;
                $green = $t;
                $blue = $p;
                break;
            case 1:
                $red = $q;
                $green = $value;
                $blue = $p;
                break;
            case 2:
                $red = $p;
                $green = $value;
                $blue = $t;
                break;
            case 3:
                $red = $p;
                $green = $q;
                $blue = $value;
                break;
            case 4:
                $red = $t;
                $green = $p;
                $blue = $value;
                break;
            case 5:
                $red = $value;
                $green = $p;
                $blue = $q;
                break;
            default:
                throw new \ErrorException('HSV Conversion RGB failure!');
                break;
        }
        ;

        return new RGB($red, $green, $blue);
    }

    /**
     * 返回数组形式表达
     * @return array
     */
    public function toArray()
    {
        return array(
            'hue' => $this->getHue(),'saturation' => $this->getSaturation(),'value' => $this->getValue()
        );
    }
}