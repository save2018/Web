<?php
/**
 * Created by Save.
 * User: admin
 * Date: 2018/10/30
 * Time: 15:38
 */

namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class User_session extends NotORM
{

    protected function getTableName($id) {
        return 'user_session';
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