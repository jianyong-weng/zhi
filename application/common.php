<?php
use phpmailer\Phpmailer;
use phpmailer\Smtp;

// 应用公共文件
function p($str) {
    echo '<pre>';
    print_r($str);
}

function nodeTree($arr, $id = 0, $level = 0) {
    static $array = array();
    foreach ($arr as $v) {
        if ($v['parentid'] == $id) {
            $v['level'] = $level;
            $array[] = $v;
            nodeTree($arr, $v['id'], $level + 1);
        }
    }
    return $array;
}

/**
 * 数组转树
 * @param type $list
 * @param type $root
 * @param type $pk
 * @param type $pid
 * @param type $child
 * @return type
 */
function list_to_tree($list, $root = 0, $pk = 'id', $pid = 'parentid', $child = '_child') {
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = 0;
            if (isset($data[$pid])) {
                $parentId = $data[$pid];
            }
            if ((string) $root == $parentId) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 下拉选择框
 */
function select($array = array(), $id = 0, $str = '', $default_option = '') {
    $string = '<select ' . $str . '>';
    $default_selected = (empty($id) && $default_option) ? 'selected' : '';
    if ($default_option)
        $string .= "<option value='' $default_selected>$default_option</option>";
    if (!is_array($array) || count($array) == 0)
        return false;
    $ids = array();
    if (isset($id))
        $ids = explode(',', $id);
    foreach ($array as $key => $value) {
        $selected = in_array($key, $ids) ? 'selected' : '';
        $string .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
    }
    $string .= '</select>';
    return $string;
}

/**
 * 复选框
 * 
 * @param $array 选项 二维数组
 * @param $id 默认选中值，多个用 '逗号'分割
 * @param $str 属性
 * @param $defaultvalue 是否增加默认值 默认值为 -99
 * @param $width 宽度
 */
function checkbox($array = array(), $id = '', $str = '', $defaultvalue = '', $width = 0, $field = '') {
    $string = '';
    $id = trim($id);
    if ($id != '')
        $id = strpos($id, ',') ? explode(',', $id) : array($id);
    if ($defaultvalue)
        $string .= '<input type="hidden" ' . $str . ' value="-99">';
    $i = 1;
    foreach ($array as $key => $value) {
        $key = trim($key);
        $checked = ($id && in_array($key, $id)) ? 'checked' : '';
        if ($width)
            $string .= '<label class="ib" style="width:' . $width . 'px">';
        $string .= '<input type="checkbox" ' . $str . ' id="' . $field . '_' . $i . '" ' . $checked . ' value="' . $key . '"> ' . $value;
        if ($width)
            $string .= '</label>';
        $i++;
    }
    return $string;
}

/**
 * 单选框
 * 
 * @param $array 选项 二维数组
 * @param $id 默认选中值
 * @param $str 属性
 */
function radio($array = array(), $id = 0, $str = '', $width = 0, $field = '') {
    $string = '';
    foreach ($array as $key => $value) {
        $checked = trim($id) == trim($key) ? 'checked' : '';
        if ($width)
            $string .= '<label class="ib" style="width:' . $width . 'px">';
        $string .= '<input type="radio" ' . $str . ' id="' . $field . '_' . $key . '" ' . $checked . ' value="' . $key . '"> ' . $value;
        if ($width)
            $string .= '</label>';
    }
    return $string;
}

/**
 * 字符串加密、解密函数
 *
 *
 * @param	string	$txt		字符串
 * @param	string	$operation	ENCODE为加密，DECODE为解密，可选参数，默认为ENCODE，
 * @param	string	$key		密钥：数字、字母、下划线
 * @param	string	$expiry		过期时间
 * @return	string
 */
function encry_code($string, $operation = 'ENCODE', $key = '', $expiry = 0) {
    $ckey_length = 4;
    $key = md5($key != '' ? $key : config('encry_key'));
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(strtr(substr($string, $ckey_length), '-_', '+/')) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . rtrim(strtr(base64_encode($result), '+/', '-_'), '=');
    }
}


//======================身份证验证 start =========================
//身份证验证
function validate_id_card($id_card) 
{ 
    if(strlen($id_card) == 18){ 
        return idcard_checksum18($id_card); 
    }elseif((strlen($id_card) == 15)){ 
        $id_card = idcard_15to18($id_card); 
        return idcard_checksum18($id_card); 
    }else{ 
        return false; 
    } 
} 
// 计算身份证校验码，根据国家标准GB 11643-1999 
function idcard_verify_number($idcard_base) 
{ 
    if(strlen($idcard_base) != 17){ 
        return false; 
    } 
    //加权因子 
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2); 
    //校验码对应值 
    $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'); 
    $checksum = 0; 
    for ($i = 0; $i < strlen($idcard_base); $i++){ 
        $checksum += substr($idcard_base, $i, 1) * $factor[$i]; 
    } 
    $mod = $checksum % 11; 
    $verify_number = $verify_number_list[$mod]; 
    return $verify_number; 
} 
// 将15位身份证升级到18位 
function idcard_15to18($idcard){ 
    if (strlen($idcard) != 15){ 
        return false; 
    }else{ 
        // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码 
        if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false){ 
            $idcard = substr($idcard, 0, 6) . '18'. substr($idcard, 6, 9); 
        }else{ 
            $idcard = substr($idcard, 0, 6) . '19'. substr($idcard, 6, 9); 
        } 
    } 
    $idcard = $idcard . idcard_verify_number($idcard); 
    return $idcard; 
} 
// 18位身份证校验码有效性检查 
function idcard_checksum18($idcard){ 
    if (strlen($idcard) != 18){ 
        return false; 
    } 
    $idcard_base = substr($idcard, 0, 17); 
    if (idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))){ 
        return false; 
    }else{ 
        return true; 
    } 
} 
//======================身份证验证 end =========================

/**
 * 获得用户的真实IP地址
 *
 *
 * @return  string $result     返回IP地址
 */
/*  */
function get_client_ip ()
{
    static $realip = NULL;
    if ($realip !== NULL)
    {
        return $realip;
    }
    if (isset($_SERVER))
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            /* 取X-Forwarded-For中第?个非unknown的有效IP字符? */
            foreach ($arr as $ip)
            {
                $ip = trim($ip);
                if ($ip != 'unknown')
                {
                    $realip = $ip;
                    break;
                }
            }
        }
        elseif (isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        }
        else
        {
            if (isset($_SERVER['REMOTE_ADDR']))
            {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
            else
            {
                $realip = '0.0.0.0';
            }
        }
    }
    else
    {
        if (getenv('HTTP_X_FORWARDED_FOR'))
        {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        }
        elseif (getenv('HTTP_CLIENT_IP'))
        {
            $realip = getenv('HTTP_CLIENT_IP');
        }
        else
        {
            $realip = getenv('REMOTE_ADDR');
        }
    }
    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = ! empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
    return $realip;
}


/**
 * 发送邮件方法
 *
 *
 * @param   string  $to         收件人邮箱
 * @param   string  $title      邮件主题
 * @param   string  $content    邮件内容
 * @param   int     $type       邮件类型
 * @return  boolean $result     发送结果
 */
   
function sendMail($to,$title,$content,$type = 1){

    //引入PHPMailer的核心文件 使用require_once包含避免出现PHPMailer类重复定义的警告
    //require_once("./class.phpmailer.php");  extend/phpmailer/Phpmailer.php
    //require_once("./class.smtp.php");     extend/phpmailer/Smtp.php
    
    //实例化PHPMailer核心类
    $mail = new Phpmailer();

    //是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
    $mail->SMTPDebug = 1;

    //使用smtp鉴权方式发送邮件
    $mail->isSMTP();

    //smtp需要鉴权 这个必须是true
    $mail->SMTPAuth = config('email.EMAIL_AUTH');

    //链接qq域名邮箱的服务器地址
    $mail->Host = config('email.EMAIL_SMTP');

    //设置使用ssl加密方式登录鉴权
    $mail->SMTPSecure = config('email.EMAIL_SECURE');

    //设置ssl连接smtp服务器的远程服务器端口号，以前的默认是25，但是现在新的好像已经不可用了 可选465或587
    $mail->Port = config('email.EMAIL_PORT');

    //设置smtp的helo消息头 这个可有可无 内容任意
    // $mail->Helo = 'Hello smtp.qq.com Server';

    //设置发件人的主机域 可有可无 默认为localhost 内容任意，建议使用你的域名
    //$mail->Hostname = 'http://www.lsgogroup.com';

    //设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
    $mail->CharSet = config('email.EMAIL_CHARSET');

    //设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
    $mail->FromName = config('email.EMAIL_FROM');

    //smtp登录的账号 这里填入字符串格式的qq号即可
    $mail->Username = config('email.EMAIL_LOGINNAME');

    //smtp登录的密码 使用生成的授权码（就刚才叫你保存的最新的授权码）
    $mail->Password = config('email.EMAIL_PASSWORD');

    //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
    $mail->From = config('email.EMAIL_ADDRESS');

    //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
    $mail->isHTML(config('email.EMAIL_HTML')); 

    //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
    $mail->addAddress($to,'');

    //添加多个收件人 则多次调用方法即可
    // $mail->addAddress('xxx@163.com','lsgo在线通知');

    //添加该邮件的主题
    $mail->Subject = $title;

    //添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
    $mail->Body = $content;

    //为该邮件添加附件 该方法也有两个参数 第一个参数为附件存放的目录（相对目录、或绝对目录均可） 第二参数为在邮件附件中该附件的名称
    // $mail->addAttachment('./d.jpg','mm.jpg');
    //同样该方法可以多次调用 上传多个附件
    // $mail->addAttachment('./Jlib-1.1.0.js','Jlib.js');

    $result = $mail->send();

    //简单的判断与提示信息
    if($result) {       
        return true;
    }else{
        return false;
    }
}


/**
 * 手机号校验
 * @param $mobile   string      手机号
 * @param $return   boolean     校验结果
 *
 */
function validateMobile($mobile)
{
    return preg_match('/^((\+86)|(86))?(1[3|5|7|8])\d{9}$/',$mobile); //  
}


/**
 * 生成随机数
 *
 * @param   int     $len        随机数长度
 * @param   string  $format     随机数格式
 * @return  string  $randstr    返回随机数
 */
function randStr($len=6,$format='ALL'){
    switch($format){
        case 'ALL':
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
        case 'SMALLALL':
                $chars='abcdefghijklmnopqrstuvwxyz0123456789';
                break;
        case 'CHAR':
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                break;
        case 'NUMBER':
                $chars='0123456789';
                break;
        default :
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
    } 
    $randstr = '';
    for($i = 0,$length = strlen($chars);$i < $len;$i++){  
        $num = mt_rand(0,$length)   ;
        $randstr .= $chars[$num];
    }
    return $randstr;
}