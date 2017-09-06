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
use think\Validate;

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

    /**
     * 忘记密码
     */
    public function forgot(){  

        $email = input('post.email');

        if( $_POST ){ 

            $res = Validate::is($email,'email');
            if($res) {
                //查找该邮箱是否在admin表中
                $where = array('email' => $email);
                $is_exist = model('admin')->searchAdmin(array('email' => $email),'id');
                if($is_exist){

                    //邮箱已注册情况下,发送邮件
                    $key     = md5(time().'newtp5'.$email);
                    $title   = 'NEW_TP5找回密码';
                    $content = "找回密码<a href='http://newtp5/admin/login/reset?email=".$email."&key=".$key."'>请点击此链接</a>,本链接半小时内点击有效。";
                    $result  = sendMail($email,$title,$content);
                    if($result){
                        //修改邮件发送记录             
                        model("mail_log")->addLog($email,$title,$content,$key,1);
                        $message = "发送成功,请前往重置！";
                    }else{
                        $message = "发送失败，请稍后重试！";
                    }

                }else{
                    $message = "该邮箱未注册！";
                }
            }else{
                $error = array();
                $error['email'] = 'error';
                $this->assign("error",$error);
            }            
            
            $this->assign("message",$message);
        }
                
        $this->view->engine->layout(false);        
        return $this->fetch('forgot');
    }

    /**
     * 重置密码
     */
    public function reset(){
        $p = input('request.');
        $error = array();

        if($_POST && $_POST['key']){

            //密码基本判断            
            if(!Validate::is($p['password'],'require|length:8,16')){ //($p['password']) < 8 || abslength($p['password']) > 16
                $error['password'] = 1;
            }
            

            if($p['repassword'] == ''){
                $error['repassword'] = 1;
            }elseif($p['repassword'] != $p['password'] ){
                $error['repassword'] = 1;
            }

            if(empty($error)){
                //查找该key是否可以使用
                $is_exist = db("mail_log")
                                ->where(array('key' => $p['key']))
                                ->field("id,email,time,status")
                                ->find();                
                
                if( $is_exist['status'] == 1 ){
                    //时间间隔需在半小时之内
                    if(time() - $is_exist['time'] < 1800){
                        //修改用户密码
                        $where = array('email' => $is_exist['email']);
                        $data  = array('password' => md5($p['password']));
                        $res   = model('admin')->editInfo( 2,$where,$data);
                        if($res){
                            //修改mail_log中记录状态
                            model("mail_log")->updateLog(array('key' => $p['key']) ,array('status' => 2));

                            $message = '密码重置成功，<a href="/admin/login/index">请登录！</a>';
                        }else{
                            $message = '密码重置失败，请重试！';
                        }
                    }else{
                        $message = '邮件超时验证。';
                    }
                    
                }elseif( $is_exist['status'] == 2 ){
                    $message = '邮件已验证。';
                }
                else{
                    $message = '找不到该条发送记录。';
                }                
            }            
            $this->assign("error",$error);
        }else{
            $message = '链接地址错误。';
        }
        $this->assign("message",$message);
        $this->assign("feedback",$p);
        $this->view->engine->layout(false);        
        return $this->fetch('reset');
    }

    /**
     * 注册新用户
     */
    public function signup(){      
        if($_POST){
            $error  = array();
            $p      = input('post.');

            $error['name'] = Validate::is($p['name'],'length:8,16');
            $error['email'] = Validate::is($p['email'],'email');


            dump($error);
            $this->assign('error',$error);
        }
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
