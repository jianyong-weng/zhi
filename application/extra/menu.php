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

/**
 * 我的菜单
 * @param 第一层key 当前module名
 * @param 第二层key  
 * @return array
 */
$menu = array(
	'admin' => array(
				'a' => array(
						'name'	=> '系统首页',
						'icon'	=> 'fa-bank',
						'url'	=> 'admin/index/index'
					),
				'b'	=> array(
						'name'	=> '管理设置',
						'icon'	=> 'fa-users',
						'url'	=> 'admin/admin/admin',
						'_child'=> array(
									'ba' =>	array('name' => '管理员管理' , 'icon' => '' , 'url'	=> 'admin/admin/index' ),
									'bb' =>	array('name' => '分组管理'   , 'icon' => '' , 'url'	=> 'admin/group/index' ),
									'bc' =>	array('name' => '个人设置'   , 'icon' => '' , 'url', '_child' =>array() )
									)
					),
				'c'	=> array(
						'name'	=> '系统设置',
						'icon'	=> 'fa-cogs',
						'url'	=> 'admin/menu/index',
						'_child'=> array(
									'ca' =>	array('name' => '菜单管理' , 'icon' => '' , 'url'	=> 'admin/menu/index' ),
									'cb' =>	array('name' => '日志管理' , 'icon' => '' , 'url'	=> 'admin/log/index'  )
									)
					)
			),
	'home'  => array()
);

return $menu;