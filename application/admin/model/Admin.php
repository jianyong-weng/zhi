<?php

/**
 *  
 * @file   admin.php  
 * @date   2016-8-30 15:22:57 
 * @author Zhenxun Du<5552123@qq.com>  
 * @version    SVN:$Id:$ 
 */

namespace application\admin\model;

use think\Db;

class Admin extends \think\Model {

    public $status = array(1 => '无效', 2 => '有效');

    public function getInfo($id) {
        $res = $this->field('id,username,lastloginip,lastlogintime,email,mobile,realname,openid,status')
                ->where(array('id' => $id))
                ->find();
        if ($res) {
            $res = $res->data;
        }

        return $res;
    }

    /**
     * 
     * @param int $userid 用户ID
     * @return Array
     */
    public function getUserGroups($uid) {

        $res = db('role_admin')->field('role_id')->where('admin_id', $uid)->select();

        $userGroups = '';
        if ($res) {
            foreach ($res as $k => $v) {
                $userGroups .= $v['role_id'] . ',';
            }
            return trim($userGroups, ',');
        } else {
            return false;
        }
    }

    /**
     * 登陆更新
     * @param int   $type   1:登陆更新,2:信息更新
     * @param array $where  查找条件
     * @param array $data   更新的数据
     */
    public function editInfo($type, $where, $data = array()) {
        
        if ($type == 1) {
            $data['lastlogintime'] = time();
            $data['lastloginip'] = ip2long(request()->ip());
        } elseif ($type == 2) {
            $data['updatetime'] = time();
        }
        $res = $this->allowField(true)->save($data,$where);        
        
        return $res;
    }

    /**
     * 查找用户
     * @param  array $where     搜索条件
     * @return int   $res       更新的数据
     */
    public function searchAdmin($where , $field = '*' ,$status = 1){

        $res = $this->field($field)
                ->where($where)
                ->where("status",$status)
                ->find();

        if ($res) {
            $res = $res->data;
        }

        return $res;
    }

    /**
     * 新增用户
     * @param  array $data      搜索条件
     * @return int   $res       更新的数据
     */
    public function createAdmin($data = array()){
        
        $data['encrypt']    = randStr(6,'SMALLALL');
        $data['reg_time']   = time();
        $data['password']   = md5($data['password'].$data['encrypt']);

        $res = $this->allowField(true)->save($data);        
        
        return $res; 
    }
}
