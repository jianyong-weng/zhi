<?php

/**
 * 登陆页
 * @date   2017-12-6 21:57
 * @author WengJianYong<396342220@qq.com>
 * @version 1.0
 */

namespace application\admin\controller;

use think\Controller;
use think\Loader;
use think\Validate;

class LoginController extends Controller
{

    /**
     * 登入
     * @auther WengJianYong
     */
    public function index()
    {
        $p  = input('post.');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $message = '';
            //用户名（或手机号）和密码
            $mobile = input('post.mobile');
            $password = input('post.password');

            //输入内容为空时
            if (!$mobile) {
                $message = '手机号不能为空';
            }
            if (!$password && $message == '') {
                $message = '密码不能为空';
            }

            //用户名或手机号不为空时，查询数据库
            if ($mobile && $message == '') {
                $info = db('admin')
                    ->field('id,realname,password,encrypt,mobile')
                    ->where('mobile', $mobile)
                    ->where('status', 1)
                    ->find();

                if (!$info) {
                    $message = '手机号未注册';
                }

                if ((md5($password.$info['encrypt']) != $info['password']) && $message == '') {
                    $message = '密码输入有误';
                }
            }
            //错误则返回
            if (!empty($message)) {
                $this->assign("message", $message);
                $this->view->engine->layout(false);
                return $this->fetch('login');
            }
            //查询到信息，则存入数据库
            session('user_id', $info['id']);
            session('user_mobile', $info['mobile']);
            session('realname', $info['realname']);
            //记住密码
            if (input('post.islogin')) {
                cookie('user_id', encry_code($info['id']));
                cookie('user_mobile', encry_code($info['mobile']));
                cookie('realname', $info['realname']);
            }
            //记录登录信息
            model('Admin')->editInfo(1, array('id'=>$info['id']));
            return $this->redirect('index/index');
        } else {
            if (session('user_id')) {
                return $this->fetch('index/index');
            }

            if (cookie('user_id')) {
                $mobile = encry_code(cookie('user_mobile'), 'DECODE');
                $info = db('admin')->field('id,password')->where('mobile', $mobile)->find();
                if ($info) {
                    //记录
                    session('user_id', $info['id']);
                    model('Admin')->editInfo(1, $info['id']);
                    return $this->redirect('index/index');
                }
            }

            $this->view->engine->layout(false);
            return $this->fetch('login');
        }
    }

    /**
     * 忘记密码
     * @author  WengJianYong 396342220@qq.com
     * @date    2017-12-13
     * @version 1.0
     * @return  [type]     [description]
     */
    public function forgot()
    {
        $email = input('post.email');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $res = Validate::is($email, 'email');
            if ($res) {
                //查找该邮箱是否在admin表中
                $where = array('email' => $email);
                $is_exist = model('admin')->searchAdmin($where, 'id');
                if ($is_exist) {

                    //邮箱已注册情况下,发送邮件
                    $key     = md5(time().config('encry_key').$email);
                    $title   = '找回密码';
                    $content = "找回密码<a href='". url('admin/login/reset', 'key='.$key, '', true) ."'>请点击此链接</a>,本链接1小时内点击有效。";
                    $is_use  = model('mail_log')->isUse($email);
                    if (!$is_use) {
                        $result  = sendMail($email, $title, $content);
                        if ($result) {
                            //修改邮件发送记录
                            model("mail_log")->addLog($email, $title, $content, $key, 1);
                            $message = "发送成功,请前往重置！".$host;
                        } else {
                            $message = "发送失败，请稍后重试！";
                        }
                    } else {
                        $message = "邮件已发送，请勿频繁操作。";
                    }
                } else {
                    $message = "该邮箱未注册！";
                }
            } else {
                $message = '请输入正确的邮箱。';
            }

            $this->assign("message", $message);
        }

        $this->view->engine->layout(false);
        return $this->fetch('forgot');
    }

    /**
     * 重置密码
     * @author WENGJIANYONG
     */
    public function reset()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $p = input('request.');
            //密码基本判断
            if (!Validate::length($p['password'], '8,16')) {
                $message = '密码长度需8~16位。';
            }

            if (!Validate::confirm($p['repassword'], 'password', $p)) {
                $message = '两次密码输入不一致。';
            }

            if (empty($message)) {
                //查找该key是否可以使用
                $is_exist = db("mail_log")
                                ->where(array('key' => $p['key']))
                                ->field("id,email,time,status")
                                ->find();

                if ($is_exist['status'] == 1) {
                    //时间间隔需在半小时之内
                    if (time() - strtotime($is_exist['time']) < 3600) {
                        //修改用户密码
                        $where = array('email' => $is_exist['email']);
                        $data  = array();
                        $data['encrypt'] = randStr();
                        $data['password'] = md5($p['password'].$data['encrypt']);
                        $res   = model('admin')->editInfo(2, $where, $data);
                        if ($res) {
                            //修改mail_log中记录状态
                            model("mail_log")->updateLog(array('key' => $p['key']), array('status' => 2));

                            $message = '密码重置成功，<a href="/admin/login/index">请登录！</a>';
                        } else {
                            $message = '密码重置失败，请重试！';
                        }
                    } else {
                        $message = '邮件超时验证。';
                    }
                } elseif ($is_exist['status'] == 2) {
                    $message = '邮件已验证。';
                } else {
                    $message = '参数错误。';
                }
            }
        } else {
            if ($_GET['key'] == '') {
                $message = '链接地址错误。';
            }
        }
        $this->assign("key", $_GET['key']);
        $this->assign("message", $message);
        $this->view->engine->layout(false);
        return $this->fetch('reset');
    }

    /**
     * 注册新用户
     */
    public function signup()
    {
        $p = input('post.');
        if ($_POST && $p['isagree'] && Validate::token($p['__hash__'], '__hash__', $p)) {//

            $error  = array();

            if (!validateMobile($p['mobile'])) {
                $error['mobile'] = 1;
            }

            if (!Validate::is($p['email'], 'email')) {
                $error['email'] = 1;
            }

            if (!Validate::length($p['password'], '6,16')) {
                $error['password'] = 1;
            }

            if (!Validate::confirm($p['repassword'], 'password', $p)
                || !Validate::length($p['password'], '6,16')) {
                $error['repassword'] = 1;
            }

            if (empty($error)) {
                //手机号是否注册
                $is_exist = Model('admin')->searchAdmin(array('mobile' => $p['mobile']), 'id');
                if (!$is_exist) {
                    $res = Model('admin')->createAdmin($p); //插入成功返回id,插入失败返回false
                    if ($res) {
                        //查询到信息，则存入数据库
                        session('user_id', $res);
                        session('user_mobile', $p['mobile']);

                        cookie('user_id', encry_code($res));
                        cookie('user_mobile', encry_code($p['mobile']));
                        //记录登录信息
                        return $this->redirect('index/index');
                    } else {
                        $message = "注册失败，请重试！";
                    }
                }
                $message = "手机号已注册，<a href='/admin/login/index'>请登录！</a>";
                $this->assign("message", $message);
            }

            $this->assign("feedback", $p);
            $this->assign('error', $error);
        }

        $this->view->engine->layout(false);
        return $this->fetch('signup');
    }


    /**
     * 登出
     */
    public function logout()
    {
        session('user_mobile', null);
        session('user_id', null);
        cookie('user_mobile', null);
        cookie('user_id', null);
        $this->view->engine->layout(false);
        return $this->fetch('login');
    }
}
