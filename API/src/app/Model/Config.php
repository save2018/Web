<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/18
 * Time: 10:54
 */

namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Config extends NotORM
{
    /**
     *是否开启位置测距功能
     */
    public  function  open_line(){

        return $this->getORM()
               ->where('name','open_line')
               ->fetchOne('value');

    }

    /**
     * 是否审核通过后显示店铺
     */
    public  function show_shenhe(){

        return $this->getORM()
            ->where('name','show_shenhe')
            ->fetchOne('value');
    }

}