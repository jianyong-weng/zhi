<?php

/**
 *  
 * @file   MailLog.php  
 * @date   2017-09-05  
 * @author WENGJIANYONG <82310763@qq.com>  
 * @version    SVN:1.0 
 */
namespace application\admin\model;

use think\Model;

class MailLog extends Model {
    
    /**
     *增加记录
     * 
     * @param  string 	$email 		接收邮箱
     * @param  string 	$title 		邮件标题
     * @param  string 	$content	邮件内容
     * @param  string 	$key 		验证码
     * @param  int  	$type  		邮件类型
     * @return int
     */
	public function addLog($email,$title,$content,$key,$type = 1){

		$data = array(
                    'email'     => $email,
                    'title'     => $title,
                    'content'   => $content,
                    'key'       => $key,
                    'type'		=> $type
            );

		$res = $this->allowField(true)->save($data);

        return $res;
	}

    /**
     *修改记录 
     *
     * @param  array    $where      搜索条件
     * @param  array    $data       修改字段
     * @return int
     */
    public function updateLog($where = array(),$data = array()){

        if(!is_array($where) || empty($where) || !is_array($data) || empty($data)){
            return false;
        }
        
        $res = $this->allowField(true)->save($data,$where);

        return $res;
    }

    /**
     *查找邮件是否已使用
     *
     * @param  array    $where      搜索条件
     * @param  array    $data       修改字段
     * @return int
     */
    public function isUse($email = ''){
        if( empty($email) ){
            return false;
        }

        $res = $this->field('id')
                    ->where('email',$email)
                    ->where('status',1)
                    ->whereTime('time','>','-1 hours')
                    ->find();
        var_dump($res['id']);
        return $res['id'];
    }
}
