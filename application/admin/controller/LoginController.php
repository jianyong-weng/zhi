<?php

/**
 *  登陆页
 * @file   Login.php  
 * @date   2016-8-23 19:52:46 
 * @author Zhenxun Du<5552123@qq.com>  
 * @version    SVN:$Id:$ 
 */

namespace application\admin\controller;

use think\Controller;
use think\Loader;

class LoginController extends Controller {

    /**
     * 登入
     */
    public function index() {       

        if ($_POST['dosubmit']) {
            $p  = input('post.');
            $username = input('post.username');
            $password = input('post.password');
            $error = array();
            if (!$username) {                
                $error['username'] = 1;                
            }
            if (!$password) {
                $error['password'] = 1;
            }

            if(!empty($error)){
                $this->assign("feedback",$p);
                $this->assign("error",$error);               
                return $this->fetch('login');
            }

            $info = db('admin')
                        ->field('id,username,password,mobile')
                        ->where('username|mobile','like', $username)
                        ->where('status',1)
                        ->find();

            if (!$info) {
                 $error['username'] = 2;              
            }

            if (md5($password) != $info['password']) {
                $error['password'] = 2;
            }

            if(!empty($error)){
                $this->assign("feedback",$p);
                $this->assign("error",$error);               
                return $this->fetch('login');
            }

            session('user_name', $info['username']);
            session('user_id', $info['id']);
            if (input('post.islogin')) {
                cookie('user_name', encry_code($info['username']));
                cookie('user_id', encry_code($info['id']));
            }

            //记录登录信息
            model('Admin')->editInfo(1, $info['id']);
            return $this->fetch('index/index');          
                        
        } else {
            if (session('user_name')) {
                $this->success('您已登入', 'index/index');
            }

            if (cookie('user_name')) {
                $username = encry_code(cookie('user_name'),'DECODE');
                $info = db('admin')->field('id,username,password')->where('username', $username)->find();
                if ($info) {
                    //记录
                    session('user_name', $info['username']);
                    session('user_id', $info['id']);
                    Loader::model('Admin')->editInfo(1, $info['id']);
                    $this->success('登入成功', 'index/index');
                }
            }

            $this->view->engine->layout(false);
            return $this->fetch('login');
        }
    }

    /**
     * 登出
     */
    public function logout() {
        session('user_name', null);
        session('user_id', null);
        cookie('user_name', null);
        cookie('user_id', null);
        $this->success('退出成功', 'login/index');
    }

}
