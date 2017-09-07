<?php

/**
 *  
 * @file   AdminGroup.php  
 * @date   2016-8-30 18:22:31 
 * @author Zhenxun Du<5552123@qq.com>  
 * @version    SVN:$Id:$ 
 */

namespace application\admin\model;

class Role extends \think\Model {

    public function getRole() {

        $res = db('role')->field('id,name')->select();
        $data = array();
        foreach ($res as $k => $v) {
            $data[$v['id']] = $v['name'];
        }
        return $data;
    }

    public function getRoleName($group_id) {
        return db('role')->where(['id' => $group_id])->value('name');
    }

}
