<?php

/**
 *  
 * @file   AdminLog.php  
 * @date   2016-10-9 17:29:09 
 * @author Zhenxun Du<5552123@qq.com>  
 * @version    SVN:$Id:$ 
 */
class AdminLog extends think\Model {
    
	/**
     * 
     * @param  array  	$data  		操作记录
     * @return int
     */
	public function addLog($data = array()){

		$res = $this->allowField(true)->save($data);

        return $res;
	}
}
