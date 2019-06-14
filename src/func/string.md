字符串
===

iconv_* 多字节, mb_*多字节

# 过滤
 * trim 删除两端的符号
 * rtrim 删除右边的符号, 别名chop 
 * ltrim 删除左边的符号
 * dirname 返回路径中目录的部分
 * htmlentities 转换html符号为实体
 * htmlspecialchars 预定义字符转html编码
 * nl2br 转移为标签
 * strip_tags 去除html,xml,php标签
 * addcslashes 在指定的字符前添加反斜线 - 自定义
 * stripcslashes 删除addcslashes添加的反斜线
 * addslashes 指定预定义字符前添加反斜线 - 自动
 * stripslashes 删除stripslashes添加的反斜线
 * quotemeta 在字符串中某些预定义的字符前添加反斜线
 * 
 * # 
 * chr 从指定的 ASCII 值返回字符
 * ord 返回字符串第一个字符的 ASCII值
 * 
 * #
 * strcasecmp 不区分大小写比较两字符串
 * strcmp 区分大小写比较两字符串
 * strncmp 比较字符串前n个字符,区分大小写
 * strncasecmp 比较字符串前n个字符,不区分大小写
 * strnatcmp 自然顺序法比较字符串长度,区分大小写
 * strnatcasecmp 自然顺序法比较字符串长度,不区分大小写
 * 
 * 
 * # 
 * str_pad 字符串填充指定长度, 重复字符串次数并追加到字符串
 * str_repate 重负字符次数
 * str_split 字符串分割为数组
 * chunk_split 将字符串分成小块
 * strtok 切开字符串
 * explode 使用一个字符串为标志分割另一个字符串
 * implode 同join,将数组值用预订字符连接成字符串
 * 
 * substr 截取字符串
 * substr_replace 替换字符串中某串为另一个字符串
 * strtr 转换字符串中的某些字符, 快, 区分大小写, 不能以少换多, 不能替换为空. 会贪婪替换
 *       // 替换为空字符串将不会处理, 替换为空格" ", 会执行.
 *       strtr("I Love you","Love",""); // I Love you
 *       // 注意只替换相同长度的内容, 即结果不是预期的`L0vEAs` 而是 `lOvEs`
 *       strtr("I Loves you", 'Love', 'L0vEA'); // I lOvEs yOu
 *       // 注意会额外的替换, you中的o也进行了替换
 *       strtr("I Love you","Lo","lO"); // I lOve yOu
 *       // 期望的正确处理, 使用数组设置替换strtr(string,array)
 *       // 但这样就比str_replace()处理要慢了!
 *       strtr("I Love you", ['Lo' => '10']); // I 10ve you
 * str_replace 字符串替换操作,区分大小写
 * str_ireplace 字符串替换操作,不区分大小写
 * number_format 千位分组来格式化数字
 * ucfirst 首字母大写
 * ucwords 每个单词首字母大写
 * 
 * # 
 * substr_count 统计一个字符串,在另一个字符串中出现次数
 * similar_text 返回两字符串相同字符的数量
 * str_word_count 统计字符串含有的单词数
 * strlen 统计字符串长度
 * count_chars 统计字符串中所有字母出现次数(0..255)
 * md5 字符串md5编码
 * 
 * # 查找
 * strchr 返回一个字符串在另一个字符串中开始位置到结束的字符串
 * strrchr 返回一个字符串在另一个字符串中最后一次出现位置开始到末尾的字符串
 * stristr 返回一个字符串在另一个字符串中开始位置到结束的字符串，不区分大小写
 * strpos 最先出现的位置
 * stripos 最先出现的位置, 不区分大小写
 * strrpos 最后出现的位置
 * strripos 最后出现的位置, 不区分大小写
 * strspn 返回字符串中首次符合mask的子字符串长度
 * strcspn 返回字符串中不符合mask的字符串的长度
 * 
 * 
 * # 处理
 * strrev 反转字符串
 * str_shuffle 随机打乱字符串
 * wordwrap 按长度队字符串折行处理
 * parse_str 将字符串解析为变量
 * 
 * #
 * mb_substr 获取字符串的部分
 * mb_http_output 设置/获取 HTTP 输出字符编码
 * mb_strlen 获取字符串的长度
 * mb_substr_count 统计字符串出现的次数
 * mb_check_encoding 检查字符串在指定的编码里是否有效
 * mb_strrpos 查找字符串在一个字符串中最后出现的位置
 * mb_split 使用正则表达式分割多字节字符串
 * iconv 字符串按要求的字符编码来转换
 * iconv_substr 截取字符串的部分
 * iconv_get_encoding 获取 iconv 扩展的内部配置变量
 * parse_url 解释URL成为一个数组	