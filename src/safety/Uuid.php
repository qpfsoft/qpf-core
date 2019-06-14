<?php
namespace qpf\safety;

/**
 * UUID
 *
 * 全拼'Universally Unique Identifier'通用唯一识别码, 一串全球唯一的(16进制)数字串;
 *
 * 标准格式为: `xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxx`
 * 五个部分分别为8个字符、4个字符、4个字符、4个字符、12个字符，中间用'-'号间隔;
 * GUID是微软对UUID标准的一种实现;
 *
 * v1 :
 * 基于时间的UUID通过计算当前时间戳、随机数和机器MAC地址得到, 使用了MAC地址保证在全球范围的唯一性,
 * 但MAC地址容易暴露, 在局域网内被退化的算法, 以IP地址代替MAC地址.
 *
 * v2 :
 * 与v1基于时间的UUID算法相同, 但会把时间戳的前4位置换为POSIX的UID或GID
 *
 * v3 :
 * 通过计算名称(name)与名字空间(namespace)的md5散列值得到,
 * 保证了相同名字空间(namespace)中不同名称(name)生成的UUID的唯一性;
 * 不同名字空间(namespace)中的UUID的唯一性；
 * 注意: 相同名字空间中相同名字的UUID重复生成是相同的;
 *
 * v5 :
 * 与v3算法类似, 只是散列值计算使用SHA1算法
 *
 * 版本推荐 3/5
 */
class Uuid
{

    /**
     * v3 基于名字的UUID - md5
     *
     * @param string $namespace 命名空间, 采用uuid值
     * @param string $name 名称, 采用随机字符串, 如果命名空间固定, 该值必须唯一
     * @return false|string
     */
    public static function v3($namespace, $name)
    {
        if (! self::is_valid($namespace))
            return false;
        
        // 获取命名空间的十六进制组件
        $nhex = str_replace(['-', '{', '}'], '', $namespace);
        
        // 二进制值
        $nstr = '';
        
        // 将命名空间UUID转换为 bits 位
        for ($i = 0; $i < strlen($nhex); $i += 2) {
            $nstr .= chr(hexdec($nhex[$i] . $nhex[$i + 1]));
        }
        
        // Calculate hash value
        $hash = md5($nstr . $name);
        
        return sprintf('%08s-%04s-%04x-%04x-%12s', 
            
            // 32 bits for "time_low"
            substr($hash, 0, 8), 
            
            // 16 bits for "time_mid"
            substr($hash, 8, 4), 
            
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 3
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000, 
            
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000, 
            
            // 48 bits for "node"
            substr($hash, 20, 12));
    }

    /**
     * v4 伪随机UUID
     *
     * - 此函数将创建重复项
     *
     * @return string
     */
    public static function v4()
    {
        // 预先播种
        mt_srand(
            crc32(
                serialize(
                    [
                        $_SERVER['REQUEST_TIME_FLOAT'],
                        $_SERVER['SERVER_ADDR'],
                        $_SERVER['SERVER_PORT'],
                        $_SERVER['HTTP_USER_AGENT'],
                        $_SERVER['REMOTE_ADDR'],
                        $_SERVER['REMOTE_PORT']
                    ])));
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', 
            
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), 
            
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff), 
            
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000, 
            
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000, 
            
            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
    }

    /**
     * v5 基于名字的UUID - sha1
     *
     * @param string $namespace uuid
     * @param string $name 随机内容
     * @return false|string
     */
    public static function v5($namespace, $name)
    {
        if (!self::is_valid($namespace))
            return false;
        
        // Get hexadecimal components of namespace
        $nhex = str_replace(['-', '{', '}'], '', $namespace);
        
        // Binary Value
        $nstr = '';
        
        // Convert Namespace UUID to bits
        for ($i = 0; $i < strlen($nhex); $i += 2) {
            $nstr .= chr(hexdec($nhex[$i] . $nhex[$i + 1]));
        }
        
        // Calculate hash value
        $hash = sha1($nstr . $name);
        
        return sprintf('%08s-%04s-%04x-%04x-%12s', 
            
            // 32 bits for "time_low"
            substr($hash, 0, 8), 
            
            // 16 bits for "time_mid"
            substr($hash, 8, 4), 
            
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 5
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000, 
            
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000, 
            
            // 48 bits for "node"
            substr($hash, 20, 12));
    }

    /**
     * 是否有效的UUID
     *
     * @param string $uuid
     * @return bool
     */
    public static function is_valid($uuid)
    {
        return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?' . 
            '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
    }
}