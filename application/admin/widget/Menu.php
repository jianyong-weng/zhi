<?php

/**
 * 菜单
 * @file   menu.php
 * @date   2017-12-17 16:18:45
 * @author WengJianYong <396342220@qq.com>
 * @version 1.0
 */

namespace application\admin\widget;

class Menu
{
    private $user_id;
    private $menu;
    private $menu_arr;

    public function __construct()
    {
        if (!session('user_id')) {
            return false;
        }
        $this->user_id = session('user_id');
        $this->menu    = $this->getMyMenu($this->user_id, 1);
    }

    public function index()
    {
        $html = '<ul class="nav nav-list">';
        $html .=$this->menuTree($this->menu);
        $html .= "</ul>";
        return $html;
    }
    /**
     * 菜单html
     * @param  $tree array 数组
     * @return $html string 字符串
     */
    public function menuTree($tree)
    {
        $html = '';
        if (is_array($tree)) {
            foreach ($tree as $val) {
                if (isset($val["name"])) {
                    $title  = $val["name"];
                    $url    = empty($val['url']) ? '' : url($val['url'], '', '', true);
                    $icon   = empty($val['icon']) ? "fa-caret-right" : $val['icon'];
                    $active = request()->url(true) == $url ? 'active' : '';

                    if (!empty($val['_child'])) {
                        $html.='<li class=""><a href="' . $url . '" class="dropdown-toggle">
                                <i class="menu-icon fa ' . $icon . '"></i>
                                <span class="menu-text"> ' . $title . ' </span>
                                <b class="arrow fa fa-angle-down"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                            ';
                        $html.=$this->menuTree($val['_child']);
                        $html.='</ul></li>';
                    } else {
                        $html.='<li class = "' . $active . '">
                                <a href = "' . $url . '">
                                <i class = "menu-icon fa ' . $icon . '"></i>
                                <span class = "menu-text"> ' . $title . ' </span>
                                </a>
                                <b class = "arrow"></b>
                                </li>
                                ';
                    }
                }
            }
        }
        return $html;
    }

    /**
     * 我的菜单
     * @param type $user_id
     * @param type $display
     * @return array
     */
    public function getMyMenu($user_id, $display = null)
    {
        /*
        $where = array();
        if ($user_id != 1) {
            $res = db('role_admin')
                    ->alias('t1')
                    ->field('t2.rules')
                    ->join(config('database.prefix').'role t2', 't1.role_id=t2.id', 'left')
                    ->where(['t1.admin_id' => $user_id])
                    ->select();

            if (!$res) {
                return false;
            }
            $tmp = '';
            foreach ($res as $k => $v) {
                $tmp .=$v['rules'] . ',';
            }

            $menu_ids = trim($tmp, ',');

            if(!$menu_ids){
                return false;
            }

            $where['id'] = ['in', $menu_ids];
        }

        if ($display) {
            $where['display'] = $display;
        }

        $res = db('menu')
                    ->where($where)
                    ->order('listorder desc')
                    ->select();
        */
        $menu = config("menu.admin");

        //找出权限表中该人物角色的权限
        return $menu;
    }
    /**
     * 获取菜单栏名称
     * @return string|false
     */
    public function getName()
    {
        $path = request()->path();
        foreach ($this->menu as $key => $value) {
            if (isset($value['_child']) && count($value['_child']) > 0) {
                foreach ($value['_child'] as $k => $child) {
                    if ($child['url'] == $path) {
                        return $child['name'];
                    }
                }
            }
        }
        return false;
    }
}
