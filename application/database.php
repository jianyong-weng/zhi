<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: WengJianYong <396342220@qq.com>
// +----------------------------------------------------------------------

$database = [
    // 数据库类型
    'type'           => 'mysql',
    // 连接dsn
    'dsn'            => '',
    // 数据库连接参数
    'params'         => [],
    // 数据库编码默认采用utf8
    'charset'        => 'utf8',
    // 数据库表前缀
    'prefix'         => 'zhi_',
    // 数据库调试模式
    'debug'          => true,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'         => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'    => false,
    // 读写分离后 主服务器数量
    'master_num'     => 1,
    // 指定从服务器序号
    'slave_no'       => '',
    // 是否严格检查字段是否存在
    'fields_strict'  => true,
    // 数据集返回类型 array 数组 collection Collection对象
    'resultset_type' => 'array',
    // 是否自动写入时间戳字段
    'auto_timestamp' => false,
    // 是否需要进行SQL性能分析
    'sql_explain'    => false,
];


switch (HOST) {
    case 'localhost':
        $database ['hostname'] = '127.0.0.1';   // 服务器地址
        $database ['database'] = 'zhi';         // 数据库名
        $database ['username'] = 'root';        // 用户名
        $database ['password'] = '';            // 密码
        $database ['hostport'] = '3306';        // 端口
        break;
    case 'zhi.cn':
        $database ['hostname'] = '127.0.0.1';   // 服务器地址
        $database ['database'] = 'zhi';         // 数据库名
        $database ['username'] = 'root';        // 用户名
        $database ['password'] = '';            // 密码
        $database ['hostport'] = '3306';        // 端口
        break;
    case 'www.zhi.cn':

    default:
        $database ['hostname'] = '127.0.0.1';   // 服务器地址
        $database ['database'] = 'zhi';         // 数据库名
        $database ['username'] = 'root';        // 用户名
        $database ['password'] = '';            // 密码
        $database ['hostport'] = '3306';        // 端口
        break;
};

return $database;

