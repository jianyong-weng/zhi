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
    public function index(){  
        $p  = input('post.');
        if($_POST){
            //用户名（或手机号）和密码
            $username = input('post.username');
            $password = input('post.password');

            $error = array();
            //输入内容为空时
            if (!$username) {                
                $error['username'] = 1;                
            }
            if (!$password) {
                $error['password'] = 1;
            }

            //用户名或手机号不为空时，查询数据库
            if($username){
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
            }
            
            //错误则返回                
            if(!empty($error)){               
                $this->assign("error",$error);               
                return $this->fetch('login');
            }
            //查询到信息，则存入数据库
            session('user_name', $info['username']);
            session('user_id', $info['id']);


            //记住密码
            if (input('post.islogin')) {
                cookie('user_name', encry_code($info['username']));
                cookie('user_id', encry_code($info['id']));
            }

            //记录登录信息
            model('Admin')->editInfo(1, $info['id']);
            return $this->fetch('index/index');
        }
        else
        {
            if (session('user_name')) {
                return $this->fetch('index/index');
            }

            if (cookie('user_name')) {
                $username = encry_code(cookie('user_name'),'DECODE');
                $info = db('admin')->field('id,username,password')->where('username', $username)->find();
                if ($info) {
                    //记录
                    session('user_name', $info['username']);
                    session('user_id', $info['id']);
                    model('Admin')->editInfo(1, $info['id']);
                    return $this->fetch('index/index');
                }
            }

            $this->view->engine->layout(false);
            return $this->fetch('login');
        }

    }

    public function forgot(){  

        $email = input('post.email');
        if($_POST && validate_email($email)){
            $array   = array(time(),'newtp5',$email);
            $key     = base64_encode(sort($array));
            $theme   = 'NEW_TP5找回密码';
            $content = '找回密码请点击<a href="http://newtp5/admin/login/reset?email=$email&key=$key">此链接</a>,本链接半小时内点击有效。';
            $result  = sendMail($email,$theme,$content);dump($result);
        }else{
            echo "error";
        }
        
        
        $this->view->engine->layout(false);        
        return $this->fetch('forgot');
    }

    public function signup(){      
       
        $this->view->engine->layout(false);        
        return $this->fetch('signup');
    }
    



    /**
     * 登出
     */
    public function logout() {
        session('user_name', null);
        session('user_id', null);
        cookie('user_name', null);
        cookie('user_id', null);
        $this->view->engine->layout(false);
        return $this->fetch('login');
    }

}
