<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c] 2006-2017 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 ]
// +----------------------------------------------------------------------
// | Author: WENGJIANYONG <396342220@qq.com>
// +----------------------------------------------------------------------


switch (HOST) {
	case 'localhost':
		defined('PUBLIC_PATH') || define('PUBLIC_PATH', 'http://localhost/zhi/public');
		break;
	case 'zhi.cn':
		defined('PUBLIC_PATH') || define('PUBLIC_PATH', 'http://zhi.cn/public');
		break;
	case 'www.zhi.cn':
		defined('PUBLIC_PATH') || define('PUBLIC_PATH', 'http://www.zhi.cn/public');
		break;
	case 'www.zhi0315.cn':
		defined('PUBLIC_PATH') || define('PUBLIC_PATH', 'http://www.zhi0315.cn/public');
		break;
	default:
		defined('PUBLIC_PATH') || define('PUBLIC_PATH', 'http://localhost/zhi/public');
		break;
}
