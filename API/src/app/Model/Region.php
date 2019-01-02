<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/2
 * Time: 14:28
 */

namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Region extends NotORM
{


    public  function  get_region(){

        //$this->getORM()->select('');



    $sql = "SELECT  region_id AS id,region_name AS name  FROM `m_region` WHERE  region_type=0";
    $rows = $this->getORM()->queryAll($sql);
    foreach($rows as $k=>$v){
        $sql = "SELECT  region_id AS id,region_name AS name  FROM `m_region` WHERE parent_id={$v['id']} and region_type=1";
        $rows[$k]['regions']= $this->getORM()->queryAll($sql);
        $regions1= $rows[$k]['regions'];
        foreach($regions1 as $k1=>$v1){
            $sql = "SELECT  region_id AS id,region_name AS name  FROM `m_region` WHERE parent_id={$v1['id']} and region_type=2";

            $regions2= $rows[$k]['regions'][$k1]['regions']= $this->getORM()->queryAll($sql);

            foreach($regions2 as $k2=>$v2){

                $sql = "SELECT  region_id AS id,region_name AS name  FROM `m_region` WHERE parent_id={$v2['id']} and region_type=3";

                $rows[$k]['regions'][$k1]['regions'][$k2]['regions']= $this->getORM()->queryAll($sql);
            }
        }
    }

       // print_r($rows);
        return $rows;

     }

    public function getListItems($state, $page, $perpage) {
        return $this->getORM()
            ->select('*')
            ->where('state', $state)
            ->order('post_date DESC')
            ->limit(($page - 1) * $perpage, $perpage)
            ->fetchAll();
    }

    public function getListTotal($state) {
        $total = $this->getORM()
            ->where('state', $state)
            ->count('id');

        return intval($total);
    }

    /**通过省市名称查询id
     * @param $name
     */
    public   function  region_id($name){

        return $this->getORM()
                ->where('region_name',$name)
                ->fetchOne('region_id');
    }
}