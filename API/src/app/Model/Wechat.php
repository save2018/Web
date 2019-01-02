<?php
/**
 * Created by Save.
 * User: admin
 * Date: 2018/10/30
 * Time: 15:38
 */

namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Wechat extends NotORM
{

    protected function getTableName($id) {
        return 'wechat';
    }

    /**
     *
     */
    public  function  insert_wechat($data){

        if($data['openid']){
            $xid=$this->getORM()->where('openid',$data['openid'])
            ->fetchOne('id');

            if(!$xid){

                $res=$this->insert($data);

            }

        }

    }

    /**
     *
     */
    public  function  get_nickName($openid){

        $nickName=$this->getORM()->where('openid',$openid)
            ->fetchOne('nickName');
        return $nickName;

    }

    public function getListItems($state, $page, $perpage) {
        return $this->getORM()
            ->select('*')
            ->where('state', $state)
            ->order('login_time DESC')
            ->limit(($page - 1) * $perpage, $perpage)
            ->fetchAll();
    }

    public function getListTotal($user_id) {
        $total = $this->getORM()
            ->where('user_id', $user_id)
            ->count('id');

        return intval($total);
    }


}