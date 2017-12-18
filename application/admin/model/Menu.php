<?php

/**
 *
 * @file   Menu.php
 * @date   2016-9-1 15:48:53
 * @author Zhenxun Du<5552123@qq.com>
 * @version    SVN:$Id:$
 */

namespace application\admin\model;

use think\Model;

class Menu extends Model
{
    public $display = array('1' => '显示', '2' => '不显示');

    public function getName()
    {
        $where = array();
        $where['c'] = request()->controller();
        $where['a'] = request()->action();

        //$res = $this->where($where)->field('id,name,parentid')->find();
        //return $res['name'];
    }

    public function getInfo()
    {
        $where = array();
        $where['c'] = request()->controller();
        $where['a'] = request()->action();
        $res = $this->where($where)->field('id,name,parentid')->find();
        return $res;
    }

    /**
     * 获取前当标题
     * @return type
     */
    public function getTitle()
    {
        $info = $this->getInfo();
        $title = '';
        if ($info->parentid) {
            $parentName = $this->where('id', $info->parentid)->value('name');

            $title = $parentName . '  <small><i class="ace-icon fa fa-angle-double-right"></i> ' . $info['name'] . '</small>';
        } else {
            $title = $info['name'];
        }
        return $title;
    }

    /**
     * 获取上级方法名
     * @return boolean
     */
    public function getParentNname()
    {
        $info = $this->getInfo();
        if ($info->parentid) {
            return $this->where('id', $info->parentid)->value('name');
        } else {
            return false;
        }
    }

    /**
     * 择选栏目
     */
    public function selectMenu()
    {
        $res = db('menu')
                ->field('id,name,parentid')
                ->order('listorder asc')
                ->select();
        $tmpArr = nodeTree($res);

        $data = array();
        foreach ($tmpArr as $k => $v) {
            $name = $v['level'] == 0 ? '<b>' . $v['name'] . '</b>' : '├─' . $v['name'];

            $name = str_repeat("│        ", $v['level']) . $name;
            $data[$v['id']] = $name;
        }
        // dump($data);
        //exit;
        return $data;
    }

    /**
     * 所有菜单
     * @return type
     */
    public function allMenu()
    {
        $res = db('menu')
                ->field('id,name,parentid')
                ->order('listorder asc')
                ->select();
        return nodeTree($res);
    }
}
