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

//定义回调URL通用的URL  
  
define('URL_CALLBACK','index.php?type=');

return [ 
    //新浪微博配置  
    'SINA' => [  
        'APP_KEY'    => '',                             //应用注册成功后分配的 APP ID  
        'APP_SECRET' => '',                             //应用注册成功后分配的KEY  
        'CALLBACK'   => URL_CALLBACK . 'sina',  
    ],  
  
    //腾讯QQ登录配置  
    'QQ' => [  
        'APP_KEY'    => '',                             //应用注册成功后分配的 APP ID  
        'APP_SECRET' => '',                             //应用注册成功后分配的KEY  
        'CALLBACK'   => URL_CALLBACK . 'qq',  
    ],  
  
    //豆瓣配置
    'DOUBAN' => [  
        'APP_KEY'    => '',                             //应用注册成功后分配的 APP ID  
        'APP_SECRET' => '',                             //应用注册成功后分配的KEY  
        'CALLBACK'   => URL_CALLBACK . 'douban',  
    ],  
  
    //微信配置 
    'WECHAT' => [  
        'APP_KEY'    => '',                             //应用注册成功后分配的 APP ID  
        'APP_SECRET' => '',                             //应用注册成功后分配的KEY  
        'CALLBACK'   => URL_CALLBACK . 'wechat',  
    ], 

    //百度配置
    'BAIDU'  =>[
        'APP_KEY'    => '',                             //应用注册成功后分配的 APP ID  
        'APP_SECRET' => '',                             //应用注册成功后分配的KEY  
        'CALLBACK'   => URL_CALLBACK . 'baidu', 
    ] 
  
]; 
