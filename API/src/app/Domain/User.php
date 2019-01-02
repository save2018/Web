<?php
namespace App\Domain;

use App\Model\User as ModelUser;

use App\Model\Wechat as ModelWechat;

class User {


   //登录查询用户密码是否正确
    public function get_user($user_id,$password,$openid) {
        $model = new ModelUser();
        return $model->getUser($user_id,$password,$openid);
    }

    //查询上级代理信息
    public  function  up_level($code_up){

        $model = new ModelUser();
        return $model->up_level($code_up);
    }

    /**
     * @param $uid
     * 查询当前用户下面所有的
     */
    public  function  get_usid($uid,$wxid,$sid){

            $model=new ModelUser();
        //查询当前
            $rs=$model->get_usid($uid,$wxid,$sid);
            return $rs;

    }

    /**
     * 根据会员id查询用户新  $us 为会员id数组
     */
    public  function  get_us($uid){

        $model=new ModelUser();
        $res=$model->get_us($uid);
        return $res;
    }
    /**
     * 保存微信用户数据
     */
    public  function  insert_wechat($newData){

        $model=new ModelWechat();
        $newData['addtime'] =  $_SERVER['REQUEST_TIME'];
        $model->insert_wechat($newData);
    }


    public function insert($newData) {
     //   $newData['post_date'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

        $model = new ModelUser();
        return $model->insert($newData);
    }

    public function update($id, $newData) {
        $model = new ModelUser();
        return $model->update($id, $newData);
    }

    public function get($id) {
        $model = new ModelUser();
        return $model->get($id);
    }

    public function delete($id) {
        $model = new ModelUser();
        return $model->delete($id);
    }

    public function getList($state, $page, $perpage) {
        $rs = array('items' => array(), 'total' => 0);

        $model = new ModelUser();
        $items = $model->getListItems($state, $page, $perpage);
        $total = $model->getListTotal($state);

        $rs['items'] = $items;
        $rs['total'] = $total;

        return $rs;
    }
}
